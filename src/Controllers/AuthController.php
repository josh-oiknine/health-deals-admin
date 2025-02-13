<?php

namespace App\Controllers;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;

class AuthController
{
  private $view;
  private $db;
  private $tfa;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
    $this->db = $container->get('db');

    // Initialize TwoFactorAuth with proper QR code provider
    $qrCodeProvider = new ImageChartsQRCodeProvider();
    $this->tfa = new TwoFactorAuth($qrCodeProvider, 'Health Deals Admin', 6, 30, Algorithm::Sha1);
  }

  public function loginPage(Request $request, Response $response)
  {
    try {
      $result = $this->view->render($response, 'auth/login.php', [
        'error' => null
      ]);

      return $result;
    } catch (Exception $e) {
      error_log('Error rendering login page: ' . $e->getMessage());
      error_log($e->getTraceAsString());
      throw $e;
    }
  }

  public function login(Request $request, Response $response)
  {
    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND is_active = true');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      if (!$user['totp_setup_complete']) {
        // User needs to set up 2FA
        $_SESSION['setup_2fa'] = true;
        $_SESSION['temp_user_id'] = $user['id'];

        return $response->withHeader('Location', '/setup-2fa')->withStatus(302);
      }

      // Check if user has a valid MFA session
      $authToken = $_COOKIE['auth_token'] ?? null;
      if ($authToken) {
        try {
          $decoded = JWT::decode($authToken, new Key($_ENV['JWT_SECRET'], 'HS256'));
          if ($decoded->user_id === $user['id'] &&
              isset($decoded->mfa_verified_until) &&
              $decoded->mfa_verified_until > time()) {
            // MFA is still valid, create new token and redirect to dashboard
            $this->createAndSetToken($user['id'], $user['email']);

            return $response->withHeader('Location', '/dashboard')->withStatus(302);
          }
        } catch (Exception $e) {
          // Token invalid or expired, continue with MFA
        }
      }

      // Store user info in session for MFA verification
      $_SESSION['mfa_required'] = true;
      $_SESSION['temp_user_id'] = $user['id'];

      return $response->withHeader('Location', '/mfa')->withStatus(302);
    }

    return $this->view->render($response, 'auth/login.php', [
      'error' => 'Invalid credentials'
    ]);
  }

  public function setup2faPage(Request $request, Response $response)
  {
    if (!isset($_SESSION['setup_2fa']) || !$_SESSION['setup_2fa']) {
      return $response->withHeader('Location', '/')->withStatus(302);
    }

    $userId = $_SESSION['temp_user_id'];
    $secret = $this->tfa->createSecret();

    // Store the secret temporarily in the session
    $_SESSION['temp_totp_secret'] = $secret;

    // Generate QR code
    $qrCodeUrl = $this->tfa->getQRCodeImageAsDataUri(
      'Health Deals Admin',
      $secret
    );

    return $this->view->render($response, 'auth/setup-2fa.php', [
      'qrCode' => $qrCodeUrl,
      'secret' => $secret,
      'error' => null
    ]);
  }

  public function setup2fa(Request $request, Response $response)
  {
    if (!isset($_SESSION['setup_2fa']) || !$_SESSION['setup_2fa']) {
      return $response->withHeader('Location', '/')->withStatus(302);
    }

    $data = $request->getParsedBody();
    $code = $data['code'] ?? '';
    $userId = $_SESSION['temp_user_id'];
    $secret = $_SESSION['temp_totp_secret'];

    if ($this->tfa->verifyCode($secret, $code)) {
      // Save the secret and mark setup as complete
      $stmt = $this->db->prepare('UPDATE users SET totp_secret = ?, totp_setup_complete = true WHERE id = ?');
      $stmt->execute([$secret, $userId]);

      // Clear setup session data
      unset($_SESSION['setup_2fa']);
      unset($_SESSION['temp_totp_secret']);

      // Redirect to MFA verification
      $_SESSION['mfa_required'] = true;

      return $response->withHeader('Location', '/mfa')->withStatus(302);
    }

    return $this->view->render($response, 'auth/setup-2fa.php', [
      'qrCode' => $this->tfa->getQRCodeImageAsDataUri('Health Deals Admin', $secret),
      'secret' => $secret,
      'error' => 'Invalid verification code'
    ]);
  }

  public function mfaPage(Request $request, Response $response)
  {
    if (!isset($_SESSION['mfa_required']) || !$_SESSION['mfa_required']) {
      return $response->withHeader('Location', '/')->withStatus(302);
    }

    return $this->view->render($response, 'auth/mfa.php', [
      'error' => null
    ]);
  }

  public function verifyMfa(Request $request, Response $response)
  {
    if (!isset($_SESSION['mfa_required']) || !$_SESSION['mfa_required']) {
      return $response->withHeader('Location', '/')->withStatus(302);
    }

    $data = $request->getParsedBody();
    $code = $data['mfa_code'] ?? '';
    $userId = $_SESSION['temp_user_id'] ?? null;

    if (!$userId) {
      return $response->withHeader('Location', '/')->withStatus(302);
    }

    $stmt = $this->db->prepare('SELECT email, totp_secret FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user && $this->tfa->verifyCode($user['totp_secret'], $code)) {
      // Clear session data
      unset($_SESSION['mfa_required']);
      unset($_SESSION['temp_user_id']);

      // Create and set JWT token
      $this->createAndSetToken($userId, $user['email']);

      return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    return $this->view->render($response, 'auth/mfa.php', [
      'error' => 'Invalid verification code'
    ]);
  }

  private function createAndSetToken(int $userId, string $email): string
  {
    $payload = [
      'user_id' => $userId,
      'email' => $email,
      'exp' => time() + (5 * 24 * 60 * 60), // 5 days
      'mfa_verified_until' => time() + (14 * 24 * 60 * 60) // MFA valid for 14 days
    ];

    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    // More permissive cookie settings for development
    $secure = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production';
    setcookie('auth_token', $jwt, [
      'expires' => time() + (14 * 24 * 60 * 60), // 14 days to match MFA verification period
      'path' => '/',
      'secure' => $secure,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);

    return $jwt;
  }

  public function logout(Request $request, Response $response)
  {
    // Update logout to match the same cookie settings
    setcookie('auth_token', '', [
      'expires' => time() - 3600,
      'path' => '/',
      'secure' => isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production',
      'httponly' => true,
      'samesite' => 'Lax'
    ]);

    return $response->withHeader('Location', '/')->withStatus(302);
  }

  // API Login
  public function apiLogin(Request $request, Response $response)
  {
    // Add CORS headers
    $response = $response
      ->withHeader('Access-Control-Allow-Origin', $_ENV['APP_URL'] ?? '*')
      ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
      ->withHeader('Access-Control-Allow-Credentials', 'true')
      ->withHeader('Content-Type', 'application/json');

    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Maybe it's a json request
    if (empty($email) || empty($password)) {
      $rawBody = $request->getBody()->__toString();
      $data = json_decode($rawBody, true);
      $email = $data['email'] ?? '';
      $password = $data['password'] ?? '';
    }

    // Validate required fields
    if (empty($email) || empty($password)) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Email and password are required'
      ]));

      return $response->withStatus(400);
    }

    $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND is_active = true');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Invalid credentials'
      ]));

      return $response->withStatus(401);
    }

    // Check if 2FA is not set up
    if (!$user['totp_setup_complete']) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Two-factor authentication setup required'
      ]));

      return $response->withStatus(403);
    }

    // Generate JWT token
    $jwt = $this->createAndSetToken($user['id'], $user['email']);

    $response->getBody()->write(json_encode([
      'status' => 'success',
      'message' => 'Authentication successful',
      'auth_token' => $jwt
    ]));

    return $response->withStatus(200);
  }

  public function apiVerifyToken(Request $request, Response $response)
  {
    // Add CORS headers
    $response = $response
      ->withHeader('Access-Control-Allow-Origin', $_ENV['APP_URL'] ?? '*')
      ->withHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, Origin')
      ->withHeader('Access-Control-Allow-Credentials', 'true')
      ->withHeader('Content-Type', 'application/json');

    // Get Authorization header
    $authHeader = $request->getHeaderLine('Authorization');
    if (empty($authHeader) || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'No token provided or invalid format'
      ]));

      return $response->withStatus(401);
    }

    $token = $matches[1];

    try {
      // Verify the token
      $jwtSecret = $_ENV['JWT_SECRET'] ?? null;
      if (!$jwtSecret) {
        error_log('AuthMiddleware: JWT_SECRET is not set in environment variables');
        throw new Exception('JWT_SECRET not configured');
      }

      // Attempt to decode the token
      $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));

      // Check if token is expired
      if (isset($decoded->exp) && $decoded->exp < time()) {
        throw new Exception('Token has expired');
      }

      $response->getBody()->write(json_encode([
        'status' => 'success',
        'message' => 'Token is valid'
      ]));

      return $response->withStatus(200);

    } catch (Exception $e) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Invalid or expired token'
      ]));

      return $response->withStatus(401);
    }
  }

  // Handle OPTIONS preflight request
  public function handleOptionsRequest(Request $request, Response $response)
  {
    return $response
      ->withHeader('Access-Control-Allow-Origin', $_ENV['APP_URL'] ?? '*')
      ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS, GET')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, Origin')
      ->withHeader('Access-Control-Allow-Credentials', 'true')
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(204);
  }
}
