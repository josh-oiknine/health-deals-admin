<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Deal;
use App\Models\Store;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DealsController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
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

    if ($request->getMethod() === 'POST') {
      try {
        $data = $request->getParsedBody();

        // Parse dates
        $startDate = new DateTime($data['start_date'] ?? 'now');
        $endDate = !empty($data['end_date']) ? new DateTime($data['end_date']) : null;

        $deal = new Deal(
          $data['title'] ?? '',
          $data['description'] ?? '',
          $data['url'] ?? '',
          !empty($data['product_id']) ? (int)$data['product_id'] : null,
          (int)($data['store_id'] ?? 0),
          !empty($data['category_id']) ? (int)$data['category_id'] : null,
          (float)($data['original_price'] ?? 0.0),
          (float)($data['deal_price'] ?? 0.0),
          $data['coupon_code'] ?? null,
          $startDate,
          $endDate,
          isset($data['is_active']) && $data['is_active'] === 'on'
        );

        if ($deal->save()) {
          return $response->withHeader('Location', '/deals')
            ->withStatus(302);
        } else {
          $error = 'Failed to save the deal. Please try again.';
        }
      } catch (Exception $e) {
        error_log("Error in DealsController::add(): " . $e->getMessage());
        $error = 'An error occurred while saving the deal.';
      }
    }

    return $this->view->render($response, 'deals/form.php', [
      'deal' => null,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'add',
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $dealData = Deal::findById($id);
    $error = null;

    if (!$dealData) {
      return $response->withHeader('Location', '/deals')
        ->withStatus(302);
    }

    $stores = Store::findAllActive();
    $categories = Category::findAllActive();

    if ($request->getMethod() === 'POST') {
      try {
        $data = $request->getParsedBody();

        // Parse dates
        $startDate = new DateTime($data['start_date'] ?? 'now');
        $endDate = !empty($data['end_date']) ? new DateTime($data['end_date']) : null;

        $deal = new Deal(
          $data['title'] ?? '',
          $data['description'] ?? '',
          $data['url'] ?? '',
          !empty($data['product_id']) ? (int)$data['product_id'] : null,
          (int)($data['store_id'] ?? 0),
          !empty($data['category_id']) ? (int)$data['category_id'] : null,
          (float)($data['original_price'] ?? 0.0),
          (float)($data['deal_price'] ?? 0.0),
          $data['coupon_code'] ?? null,
          $startDate,
          $endDate,
          isset($data['is_active']) && $data['is_active'] === 'on'
        );
        $deal->setId($id);

        if ($deal->save()) {
          return $response->withHeader('Location', '/deals')
            ->withStatus(302);
        } else {
          $error = 'Failed to update the deal. Please try again.';
        }
      } catch (Exception $e) {
        error_log("Error in DealsController::edit(): " . $e->getMessage());
        $error = 'An error occurred while updating the deal.';
      }
    }

    return $this->view->render($response, 'deals/form.php', [
      'deal' => $dealData,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'edit',
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    Deal::delete($id);

    return $response->withHeader('Location', '/deals')
      ->withStatus(302);
  }
}
