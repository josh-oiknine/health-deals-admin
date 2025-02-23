<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Deal;
use App\Models\Product;
use App\Models\ScrapingJob;
use App\Models\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
  }

  public function index(Request $request, Response $response): Response
  {
    $metrics = [
      'activeProducts' => Product::countActive(),
      'productsPerDay' => Product::getProductsPerDay(7),
      'activeDeals' => Deal::countActive(),
      'dealsPerDay' => Deal::getDealsPerDay(7),
      'activeStores' => Store::countActive(),
      'activeCategories' => Category::countActive(),
      'messagesSentToday' => 0, // TODO: Implement when Outbox model is ready,
      'pendingJobsCount' => ScrapingJob::findCountByStatus('pending'),
      'runningJobsCount' => ScrapingJob::findCountByStatus('running'),
      'completedJobsCount' => ScrapingJob::findCountByStatus('completed') + ScrapingJob::findCountByStatus('stopped'),
      'failedJobsCount' => ScrapingJob::findCountByStatus('failed')
    ];

    $latestDeals = Deal::getLatestDeals(10);

    return $this->view->render($response, 'dashboard/index.php', [
      'title' => 'Dashboard',
      'metrics' => $metrics,
      'latestDeals' => $latestDeals
    ]);
  }
}
