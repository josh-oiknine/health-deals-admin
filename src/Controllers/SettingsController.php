<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RobThree\Auth\Algorithm;
use RobThree\Auth\Providers\Qr\ImageChartsQRCodeProvider;
use RobThree\Auth\TwoFactorAuth;

class SettingsController
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

  public function index(Request $request, Response $response)
  {
    return $this->view->render($response, 'settings/index.php', [
      'error' => null,
      'success' => null
    ]);
  }

  public function changePassword(Request $request, Response $response)
  {
    $data = $request->getParsedBody();
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';

    // Get current user
    $userId = $request->getAttribute('user_id');
    $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    // Validate current password
    if (!password_verify($currentPassword, $user['password'])) {
      return $this->view->render($response, 'settings/index.php', [
        'error' => 'Current password is incorrect',
        'success' => null
      ]);
    }

    // Validate new password
    if ($newPassword !== $confirmPassword) {
      return $this->view->render($response, 'settings/index.php', [
        'error' => 'New passwords do not match',
        'success' => null
      ]);
    }

    if (strlen($newPassword) < 8) {
      return $this->view->render($response, 'settings/index.php', [
        'error' => 'New password must be at least 8 characters long',
        'success' => null
      ]);
    }

    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
    $stmt->execute([$hashedPassword, $userId]);

    return $this->view->render($response, 'settings/index.php', [
      'error' => null,
      'success' => 'Password updated successfully'
    ]);
  }

  public function changeMfaDevice(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $secret = $this->tfa->createSecret();

    // Store the secret temporarily in the session
    $_SESSION['temp_totp_secret'] = $secret;

    // Generate QR code
    $qrCodeUrl = $this->tfa->getQRCodeImageAsDataUri(
      'Health Deals Admin',
      $secret
    );

    return $this->view->render($response, 'settings/change-mfa.php', [
      'qrCode' => $qrCodeUrl,
      'secret' => $secret,
      'error' => null
    ]);
  }

  public function verifyNewMfaDevice(Request $request, Response $response)
  {
    $data = $request->getParsedBody();
    $code = $data['code'] ?? '';
    $userId = $request->getAttribute('user_id');
    $secret = $_SESSION['temp_totp_secret'] ?? null;

    if (!$secret) {
      return $response->withHeader('Location', '/settings')->withStatus(302);
    }

    if ($this->tfa->verifyCode($secret, $code)) {
      // Save the new secret
      $stmt = $this->db->prepare('UPDATE users SET totp_secret = ? WHERE id = ?');
      $stmt->execute([$secret, $userId]);

      // Clear temporary session data
      unset($_SESSION['temp_totp_secret']);

      return $this->view->render($response, 'settings/index.php', [
        'error' => null,
        'success' => 'MFA device updated successfully'
      ]);
    }

    return $this->view->render($response, 'settings/change-mfa.php', [
      'qrCode' => $this->tfa->getQRCodeImageAsDataUri('Health Deals Admin', $secret),
      'secret' => $secret,
      'error' => 'Invalid verification code'
    ]);
  }
}
