<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class UsersController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php');
  }

  private function getCurrentUserEmail(Request $request): ?string
  {
    $token = $_COOKIE['auth_token'] ?? null;
    if (!$token) {
      return null;
    }

    try {
      $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
      return $decoded->email ?? null;
    } catch (\Exception $e) {
      return null;
    }
  }

  public function index(Request $request, Response $response): Response
  {
    // Only allow access to josh@udev.com
    $currentUserEmail = $this->getCurrentUserEmail($request);
    if ($currentUserEmail !== 'josh@udev.com') {
      throw new HttpNotFoundException($request);
    }

    $users = User::findAll();

    return $this->view->render($response, 'users/index.php', [
      'users' => $users
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    // Only allow access to josh@udev.com
    $currentUserEmail = $this->getCurrentUserEmail($request);
    if ($currentUserEmail !== 'josh@udev.com') {
      throw new HttpNotFoundException($request);
    }

    $error = null;
    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("User data received: " . print_r($data, true));

      // Check if email already exists
      if (User::findByEmail($data['email'] ?? '')) {
        $error = "A user with this email already exists.";
      } else {
        $user = new User(
          $data['email'] ?? '',
          $data['password'] ?? '',
          $data['first_name'] ?? '',
          $data['last_name'] ?? '',
          ($data['is_active'] ?? '') === 'on'
        );

        if ($user->save()) {
          return $response->withHeader('Location', '/users')
            ->withStatus(302);
        }
        error_log("Failed to save user");
        $error = "Failed to save user. Please try again.";
      }
    }

    return $this->view->render($response, 'users/form.php', [
      'user' => new User(),
      'isEdit' => false,
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    // Only allow access to josh@udev.com
    $currentUserEmail = $this->getCurrentUserEmail($request);
    if ($currentUserEmail !== 'josh@udev.com') {
      throw new HttpNotFoundException($request);
    }

    $id = (int)$args['id'];
    $user = User::findById($id);
    if (!$user) {
      throw new HttpNotFoundException($request);
    }

    $error = null;
    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("User edit data received: " . print_r($data, true));

      // Check if email is changed and already exists
      if ($data['email'] !== $user->getEmail() && User::findByEmail($data['email'] ?? '')) {
        $error = "A user with this email already exists.";
      } else {
        $user->setEmail($data['email'] ?? '');
        if (!empty($data['password'])) {
          $user->setPassword($data['password']);
        }
        $user->setFirstName($data['first_name'] ?? '');
        $user->setLastName($data['last_name'] ?? '');
        $user->setIsActive(($data['is_active'] ?? '') === 'on');

        if ($user->save()) {
          return $response->withHeader('Location', '/users')
            ->withStatus(302);
        }
        error_log("Failed to update user");
        $error = "Failed to update user. Please try again.";
      }
    }

    return $this->view->render($response, 'users/form.php', [
      'user' => $user,
      'isEdit' => true,
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    // Only allow access to josh@udev.com
    $currentUserEmail = $this->getCurrentUserEmail($request);
    if ($currentUserEmail !== 'josh@udev.com') {
      throw new HttpNotFoundException($request);
    }

    $id = (int)$args['id'];
    $user = User::findById($id);
    if ($user) {
      $user->softDelete();
    }

    return $response->withHeader('Location', '/users')
      ->withStatus(302);
  }

  public function removeMfa(Request $request, Response $response, array $args): Response
  {
    // Only allow access to josh@udev.com
    $currentUserEmail = $this->getCurrentUserEmail($request);
    if ($currentUserEmail !== 'josh@udev.com') {
      throw new HttpNotFoundException($request);
    }

    $id = (int)$args['id'];
    $user = User::findById($id);
    if ($user) {
      $user->removeMfa($id);
    }

    return $response->withHeader('Location', '/users')
      ->withStatus(302);
  }
} 