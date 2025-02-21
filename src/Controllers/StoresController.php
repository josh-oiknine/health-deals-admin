<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class StoresController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
  }

  public function index(Request $request, Response $response): Response
  {
    $stores = Store::findAll();

    return $this->view->render($response, 'stores/index.php', [
      'stores' => $stores
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    $error = null;
    $data = [];

    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("Store data received: " . print_r($data, true));

      $store = new Store(
        $data['name'] ?? '',
        $data['logo_url'] ?? null,
        $data['url'] ?? null,
        isset($data['is_active']) && $data['is_active'] === 'on'
      );

      if ($store->save()) {
        return $response->withHeader('Location', '/stores')
          ->withStatus(302);
      }
      error_log("Failed to save store");
      $error = "Failed to save store. Please try again.";
    }

    return $this->view->render($response, 'stores/form.php', [
      'store' => $data,
      'isEdit' => false,
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $store = Store::findById((int)$args['id']);
    echo '<pre>';
    print_r($store);
    echo '</pre>';
    die();
    if (!$store) {
      throw new HttpNotFoundException($request);
    }

    $error = null;
    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("Store edit data received: " . print_r($data, true));
      error_log("Is active in form data: " . (isset($data['is_active']) ? 'yes' : 'no'));

      $store->setName($data['name'] ?? '');
      $store->setLogoUrl($data['logo_url'] ?? null);
      $store->setUrl($data['url'] ?? null);

      // Convert checkbox value to boolean
      $isActive = ($data['is_active'] ?? '') === 'on';
      error_log("Setting is_active to: " . ($isActive ? 'true' : 'false'));
      $store->setIsActive($isActive);

      if ($store->save()) {
        return $response->withHeader('Location', '/stores')
          ->withStatus(302);
      }
      error_log("Failed to update store");
      $error = "Failed to update store. Please try again.";
    }

    return $this->view->render($response, 'stores/form.php', [
      'store' => $store,
      'isEdit' => true,
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $store = Store::findById((int)$args['id']);
    if ($store) {
      $store->softDelete();
    }

    return $response->withHeader('Location', '/stores')
      ->withStatus(302);
  }
}
