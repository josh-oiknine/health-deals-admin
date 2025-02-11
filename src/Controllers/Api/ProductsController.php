<?php

namespace App\Controllers\Api;

use App\Services\ProductScraperService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductsController
{
  private ProductScraperService $scraperService;

  public function __construct(ProductScraperService $scraperService)
  {
    $this->scraperService = $scraperService;
  }

  public function fetchInfo(Request $request, Response $response): Response
  {
    $params = $request->getQueryParams();
    $url = $params['url'] ?? '';

    if (empty($url)) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'URL parameter is required'
      ]));

      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Determine store type from URL
    if (strpos($url, 'amazon.com') !== false) {
      $result = $this->scraperService->scrapeAmazonProduct($url);
    } elseif (strpos($url, 'walmart.com') !== false) {
      $result = $this->scraperService->scrapeWalmartProduct($url);
    } elseif (strpos($url, 'target.com') !== false) {
      $result = $this->scraperService->scrapeTargetProduct($url);
    } else {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'Unsupported store. Currently supporting: Amazon, Walmart, Target'
      ]));

      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $response->getBody()->write(json_encode($result));

    return $response->withHeader('Content-Type', 'application/json')
      ->withStatus($result['success'] ? 200 : 400);
  }
}
