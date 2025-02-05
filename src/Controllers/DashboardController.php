<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
  }

  public function index(Request $request, Response $response): Response
  {
    $metrics = [
      'activeStores' => Store::countActive(),
      'activeProducts' => Product::countActive(),
      'activeCategories' => Category::countActive(),
      'messagesSentToday' => 0, // TODO: Implement when Outbox model is ready
    ];

    $latestDeals = Product::getLatestDeals();

    return $this->view->render($response, 'dashboard/index.php', [
      'metrics' => $metrics,
      'latestDeals' => $latestDeals
    ]);
  }
}
