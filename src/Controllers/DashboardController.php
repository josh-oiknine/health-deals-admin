<?php

namespace App\Controllers;

use App\Models\BlogPost;
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
      // Products
      'activeProducts' => Product::countActive(),
      'inactiveProducts' => Product::countInactive(),
      'productsPerDay' => Product::getProductsPerDay(7),

      // Deals
      'activeDeals' => Deal::countActive(),
      'inactiveDeals' => Deal::countInactive(),
      'dealsPerDay' => Deal::getDealsPerDay(7),

      // Blog Posts
      'activeBlogPosts' => BlogPost::countPublished(),
      'draftBlogPosts' => BlogPost::countDrafts(),

      // Stores
      'activeStores' => Store::countActive(),
      'inactiveStores' => Store::countInactive(),

      // Categories
      'activeCategories' => Category::countActive(),

      // Messages
      'messagesSentToday' => 0, // TODO: Implement when Outbox model is ready,

      // Scraping Jobs
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
