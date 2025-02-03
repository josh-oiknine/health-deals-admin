<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use PDO;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider;
use RobThree\Auth\Algorithm;

class AuthController
{
    private $view;
    private $db;
    private $tfa;

    public function __construct($container)
    {
        $this->view = $container->get('view');
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
        } catch (\Exception $e) {
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

        $stmt = $this->db->prepare('SELECT totp_secret FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user && $this->tfa->verifyCode($user['totp_secret'], $code)) {
            // Clear session data
            unset($_SESSION['mfa_required']);
            unset($_SESSION['temp_user_id']);

            // Create and set JWT token
            $this->createAndSetToken($userId, $response);

            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        return $this->view->render($response, 'auth/mfa.php', [
            'error' => 'Invalid verification code'
        ]);
    }

    private function createAndSetToken(int $userId, Response $response): void
    {
        $payload = [
            'user_id' => $userId,
            'exp' => time() + (14 * 24 * 60 * 60) // 14 days
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
        
        // More permissive cookie settings for development
        $secure = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production';
        setcookie('auth_token', $jwt, [
            'expires' => time() + (14 * 24 * 60 * 60),
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
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
} 