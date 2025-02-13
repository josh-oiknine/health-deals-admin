<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Product;
use App\Services\ProductScraperService;
use DOMXPath;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DealsController
{
  public function fetchInfo(Request $request, Response $response): Response
  {
    $scraperService = new ProductScraperService();

    $url = $request->getQueryParams()['url'] ?? '';

    if (empty($url)) {
      return $this->jsonResponse($response, [
        'success' => false,
        'error' => 'URL is required'
      ], 400);
    }

    try {
      // Pull out the SKU/ASIN/TCIN form the URL
      $sku = ProductScraperService::extractSkuFromUrl($url);

      // First, try to find the product in our database
      $product = Product::findBySku($sku);

      if (!$product) {
        return $this->jsonResponse($response, [
          'success' => false,
          'error' => 'Product not found for SKU: ' . $sku
        ], 404);
      }

      if (strpos($url, 'amazon.com') !== false) {
        $result = $scraperService->scrapeAmazonProduct($url);
      } elseif (strpos($url, 'walmart.com') !== false) {
        $result = $scraperService->scrapeWalmartProduct($url);
      } elseif (strpos($url, 'target.com') !== false) {
        $result = $scraperService->scrapeTargetProduct($url);
      } else {
        $response->getBody()->write(json_encode([
          'success' => false,
          'error' => 'Unsupported store. Currently supporting: Amazon, Walmart, Target'
        ]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
      }

      $result['product'] = $product;

      return $this->jsonResponse($response, $result);
    } catch (Exception $e) {
      error_log("Error in DealsController::fetchInfo: " . $e->getMessage());

      return $this->jsonResponse($response, [
        'success' => false,
        'error' => 'Failed to fetch product information'
      ], 500);
    }
  }

  private function extractTitle(DOMXPath $xpath): string
  {
    // Try different meta tags and elements for title
    $queries = [
      '//meta[@property="og:title"]/@content',
      '//meta[@name="twitter:title"]/@content',
      '//h1[contains(@class, "product-title")]',
      '//h1[contains(@class, "title")]',
      '//h1',
    ];

    foreach ($queries as $query) {
      $nodes = $xpath->query($query);
      if ($nodes && $nodes->length > 0) {
        return trim($nodes->item(0)->nodeValue);
      }
    }

    return '';
  }

  private function extractDescription(DOMXPath $xpath): string
  {
    // Try different meta tags and elements for description
    $queries = [
      '//meta[@property="og:description"]/@content',
      '//meta[@name="description"]/@content',
      '//meta[@name="twitter:description"]/@content',
      '//div[contains(@class, "product-description")]',
      '//div[contains(@class, "description")]',
    ];

    foreach ($queries as $query) {
      $nodes = $xpath->query($query);
      if ($nodes && $nodes->length > 0) {
        return trim($nodes->item(0)->nodeValue);
      }
    }

    return '';
  }

  private function extractImage(DOMXPath $xpath): string
  {
    // Try different meta tags and elements for image
    $queries = [
      '//meta[@property="og:image"]/@content',
      '//meta[@name="twitter:image"]/@content',
      '//img[contains(@class, "product-image")]/@src',
      '//img[contains(@class, "main-image")]/@src',
    ];

    foreach ($queries as $query) {
      $nodes = $xpath->query($query);
      if ($nodes && $nodes->length > 0) {
        return trim($nodes->item(0)->nodeValue);
      }
    }

    return '';
  }

  private function jsonResponse(Response $response, array $data, int $status = 200): Response
  {
    $response->getBody()->write(json_encode($data));

    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus($status);
  }
}
