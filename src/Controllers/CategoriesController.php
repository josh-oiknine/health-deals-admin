<?php

namespace App\Controllers;

use App\Models\Category;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class CategoriesController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
  }

  public function index(Request $request, Response $response): Response
  {
    $categories = Category::findAll();

    return $this->view->render($response, 'categories/index.php', [
      'categories' => $categories
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    $error = null;
    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("Category data received: " . print_r($data, true));

      $category = new Category(
        $data['name'] ?? '',
        $data['slug'] ?? '',
        ($data['is_active'] ?? '') === 'on'
      );

      if ($category->save()) {
        return $response->withHeader('Location', '/categories')
          ->withStatus(302);
      }
      error_log("Failed to save category");
      $error = "Failed to save category. Please try again.";
    }

    return $this->view->render($response, 'categories/form.php', [
      'category' => new Category(),
      'isEdit' => false,
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $category = Category::findById((int)$args['id']);
    if (!$category) {
      throw new HttpNotFoundException($request);
    }

    $error = null;
    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("Category edit data received: " . print_r($data, true));

      $category->setName($data['name'] ?? '');
      $category->setSlug($data['slug'] ?? '');
      $category->setIsActive(($data['is_active'] ?? '') === 'on');

      if ($category->save()) {
        return $response->withHeader('Location', '/categories')
          ->withStatus(302);
      }
      error_log("Failed to update category");
      $error = "Failed to update category. Please try again.";
    }

    return $this->view->render($response, 'categories/form.php', [
      'category' => $category,
      'isEdit' => true,
      'error' => $error
    ]);
  }
}
