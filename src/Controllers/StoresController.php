<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
      'title' => 'Stores',
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
        isset($data['is_active']) && $data['is_active'] === 'on',
      );

      if ($store->save()) {
        return $response->withHeader('Location', '/stores')
          ->withStatus(302);
      }
      error_log("Failed to save store");
      $error = "Failed to save store. Please try again.";
    }

    return $this->view->render($response, 'stores/form.php', [
      'title' => 'Add Store',
      'store' => $data,
      'isEdit' => false,
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $error = null;
    if ($request->getMethod() === 'POST') {
      $storeData = $request->getParsedBody();
      $store = new Store(
        $storeData['name'] ?? '',
        $storeData['logo_url'] ?? null,
        $storeData['url'] ?? null,
        isset($storeData['is_active']) && $storeData['is_active'] === 'on',
      );
      $store->setId($id);

      if ($store->save()) {
        return $response->withHeader('Location', '/stores')
          ->withStatus(302);
      }

      $error = "Failed to update store. Please try again.";
    } else {
      $storeData = Store::findById($id);
    }

    return $this->view->render($response, 'stores/form.php', [
      'title' => 'Edit Store',
      'store' => $storeData,
      'isEdit' => true,
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $store = new Store();
    $store->setId((int)$args['id']);
    $store->softDelete();

    return $response->withHeader('Location', '/stores')
      ->withStatus(302);
  }
}
