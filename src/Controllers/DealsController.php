<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Deal;
use App\Models\Store;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DealsController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
  }

  public function index(Request $request, Response $response): Response
  {
    // Get query parameters
    $queryParams = $request->getQueryParams();
    $page = max(1, intval($queryParams['page'] ?? 1));
    $perPage = max(1, min(100, intval($queryParams['per_page'] ?? 20)));

    // Get filter parameters
    $filters = [
      'keyword' => $queryParams['keyword'] ?? '',
      'store_id' => !empty($queryParams['store_id']) ? (int)$queryParams['store_id'] : null,
      'category_id' => isset($queryParams['category_id']) && $queryParams['category_id'] !== ''
          ? ($queryParams['category_id'] === '0' ? 0 : (int)$queryParams['category_id'])
          : null
    ];

    // Handle active/inactive filter
    if (isset($queryParams['is_active']) && $queryParams['is_active'] !== '') {
      $filters['is_active'] = $queryParams['is_active'] === '1';
    }

    // Get sorting parameters
    $sortBy = $queryParams['sort_by'] ?? 'created_at';
    $sortOrder = strtoupper($queryParams['sort_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';

    // Get filtered deals
    $result = Deal::findFiltered($filters, $sortBy, $sortOrder, $page, $perPage);

    // Get all stores, categories, and products for filters
    $stores = Store::findAllActive();
    $categories = Category::findAllActive();

    return $this->view->render($response, 'deals/index.php', [
      'title' => 'Deals',
      'deals' => $result['data'],
      'pagination' => [
        'current_page' => $result['page'],
        'per_page' => $result['per_page'],
        'total' => $result['total'],
        'last_page' => $result['last_page']
      ],
      'filters' => $filters,
      'sorting' => [
        'sort_by' => $sortBy,
        'sort_order' => $sortOrder
      ],
      'stores' => $stores,
      'categories' => $categories
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    $stores = Store::findAllActive();
    $categories = Category::findAllActive();
    $error = null;
    $formData = null;
    $dealData = [];

    if ($request->getMethod() === 'POST') {
      try {
        $dealData = $request->getParsedBody();

        $deal = new Deal(
          $dealData['title'],
          $dealData['description'],
          $dealData['affiliate_url'],
          $dealData['image_url'],
          (int)$dealData['product_id'],
          (int)$dealData['store_id'],
          !empty($dealData['category_id']) ? (int)$dealData['category_id'] : null,
          (float)$dealData['original_price'],
          (float)$dealData['deal_price'],
          isset($dealData['is_active']) && $dealData['is_active'] === 'on' ? true : false,
          isset($dealData['is_featured']) && $dealData['is_featured'] === 'on' ? true : false,
          isset($data['is_expired']) && $data['is_expired'] === 'on' ? true : false
        );

        if ($deal->save()) {
          return $response->withHeader('Location', '/deals')
            ->withStatus(302);
        } else {
          $error = 'Failed to save the deal. Please check your input and try again.';
        }
      } catch (Exception $e) {
        $error = $e->getMessage();
      }
    }

    return $this->view->render($response, 'deals/form.php', [
      'title' => 'Add Deal',
      'deal' => $dealData,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'add',
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $error = null;
    $dealData = [];

    if ($request->getMethod() === 'POST') {
      try {
        $dealData = $request->getParsedBody();
        
        $deal = new Deal(
          $dealData['title'],
          $dealData['description'],
          $dealData['affiliate_url'],
          $dealData['image_url'],
          (int)$dealData['product_id'],
          (int)$dealData['store_id'],
          !empty($dealData['category_id']) ? (int)$dealData['category_id'] : null,
          (float)$dealData['original_price'],
          (float)$dealData['deal_price'],
          isset($dealData['is_active']) && $dealData['is_active'] === 'on' ? true : false,
          isset($dealData['is_featured']) && $dealData['is_featured'] === 'on' ? true : false,
          isset($dealData['is_expired']) && $dealData['is_expired'] === 'on' ? true : false
        );
        $deal->setId($id);

        if ($deal->save()) {
          return $response->withHeader('Location', '/deals')
            ->withStatus(302);
        } else {
          $error = 'Failed to save the deal update. Please check your input and try again.';
        }
      } catch (Exception $e) {
        error_log("Error in DealsController::edit(): " . $e->getMessage());
        $error = 'Failed '.$e->getMessage();
      }
    }

    $dealData = Deal::findById($id);
    if (!$dealData) {
      return $response->withHeader('Location', '/deals')
        ->withStatus(302);
    }

    $stores = Store::findAllActive();
    $categories = Category::findAllActive();

    return $this->view->render($response, 'deals/form.php', [
      'title' => 'Edit Deal',
      'deal' => $dealData,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'edit',
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $deal = new Deal();
    $deal->setId((int)$args['id']);
    $deal->softDelete();

    return $response->withHeader('Location', '/deals')
      ->withStatus(302);
  }
}
