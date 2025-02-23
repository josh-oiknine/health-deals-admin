<?php

namespace App\Controllers;

use App\Models\Category;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CategoriesController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
  }

  public function index(Request $request, Response $response): Response
  {
    $categories = Category::findAll();

    return $this->view->render($response, 'categories/index.php', [
      'title' => 'Categories',
      'categories' => $categories
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    $error = null;
    $data = [];

    if ($request->getMethod() === 'POST') {
      $data = $request->getParsedBody();
      error_log("Category data received: " . print_r($data, true));

      $category = new Category(
        $data['name'] ?? '',
        $data['slug'] ?? '',
        ($data['is_active'] ?? '') === 'on',
        $data['color'] ?? '#6c757d'
      );

      if ($category->save()) {
        return $response->withHeader('Location', '/categories')
          ->withStatus(302);
      }
      error_log("Failed to save category");
      $error = "Failed to save category. Please try again.";
    }

    return $this->view->render($response, 'categories/form.php', [
      'title' => 'Add Category',
      'category' => $data,
      'isEdit' => false,
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $error = null;

    if ($request->getMethod() === 'POST') {
      $categoryData = $request->getParsedBody();
      error_log("Category edit data received: " . print_r($categoryData, true));

      $category = new Category(
        $categoryData['name'] ?? '',
        $categoryData['slug'] ?? '',
        ($categoryData['is_active'] ?? '') === 'on',
        $categoryData['color'] ?? '#6c757d'
      );
      $category->setId($id);
      if ($category->save()) {
        return $response->withHeader('Location', '/categories')
          ->withStatus(302);
      }
      error_log("Failed to update category");
      $error = "Failed to update category. Please try again.";
    } else {
      $categoryData = Category::findById($id);
    }

    return $this->view->render($response, 'categories/form.php', [
      'title' => 'Edit Category',
      'category' => $categoryData,
      'isEdit' => true,
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $category = new Category();
    $category->setId((int)$args['id']);
    $category->softDelete();

    return $response->withHeader('Location', '/categories')
      ->withStatus(302);
  }

  ///////////////////////////////////////////////////////////////////////////////
}
