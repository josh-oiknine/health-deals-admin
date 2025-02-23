<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ScrapingJob;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ScrapingJobsController
{
  private $view;
  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php'); // Set default layout
  }

  public function index(Request $request, Response $response): Response
  {
    $queryParams = $request->getQueryParams();
    $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
    $perPage = isset($queryParams['per_page']) ? (int)$queryParams['per_page'] : 20;
    $sortBy = $queryParams['sort_by'] ?? 'created_at';
    $sortOrder = $queryParams['sort_order'] ?? 'DESC';
    $filters = [
      'status' => $queryParams['status'] ?? null,
      'job_type' => $queryParams['job_type'] ?? null,
      'product_id' => $queryParams['product_id'] ?? null
    ];

    $jobs = ScrapingJob::findFiltered($filters, $sortBy, $sortOrder, $page, $perPage);

    $pendingJobsCount = ScrapingJob::findCountByStatus('pending');
    $runningJobsCount = ScrapingJob::findCountByStatus('running');
    $completedJobsCount = ScrapingJob::findCountByStatus('completed') + ScrapingJob::findCountByStatus('stopped');
    $failedJobsCount = ScrapingJob::findCountByStatus('failed');

    return $this->view->render($response, 'scraping-jobs/index.php', [
      'jobs' => $jobs['data'],
      'pendingJobsCount' => $pendingJobsCount,
      'runningJobsCount' => $runningJobsCount,
      'completedJobsCount' => $completedJobsCount,
      'failedJobsCount' => $failedJobsCount,
      'title' => 'Scraping Jobs',
      'pagination' => [
        'current_page' => $jobs['page'],
        'per_page' => $jobs['per_page'],
        'total' => $jobs['total'],
        'last_page' => $jobs['last_page']
      ],
      'filters' => $filters,
      'sort_by' => $sortBy,
      'sort_order' => $sortOrder
    ]);
  }

  public function add(Request $request, Response $response): Response
  {
    $error = null;

    $data = $request->getParsedBody();

    $job = new ScrapingJob(
      (int)$data['product_id'],
      $data['job_type'] ?? 'on-demand',
      $data['status'] ?? 'pending'
    );

    if ($job->save()) {
      return $response->withHeader('Location', '/products')->withStatus(302);
    } else {
      $error = 'Failed to create scraping job.';
    }

    return $this->view->render($response, 'scraping-jobs/index.php', [
      'error' => $error
    ]);
  }

  public function stop(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $job = new ScrapingJob(0);
    $job->setId($id);
    $error = null;

    if ($job->stop()) {
      return $response->withHeader('Location', '/scraping-jobs')->withStatus(302);
    } else {
      $error = 'Failed to stop scraping job.';
    }

    return $this->view->render($response, 'scraping-jobs/index.php', [
      'error' => $error
    ]);
  }
}
