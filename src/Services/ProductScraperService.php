<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ProductScraperService
{
  private Client $client;

  public function __construct()
  {
    $this->client = new Client([
      'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
      ]
    ]);
  }

  public function scrapeAmazonProduct(string $url): array
  {
    try {
      // Extract ASIN from URL
      preg_match('/\/dp\/([A-Z0-9]{10})/', $url, $matches);
      $asin = $matches[1] ?? null;

      if (!$asin) {
        throw new Exception('Invalid Amazon URL');
      }

      // Fetch the page content
      $response = $this->client->get($url);
      $html = (string) $response->getBody();

      // Create a new Crawler
      $crawler = new Crawler($html);

      // Extract product information
      $name = $this->extractProductName($crawler);
      $price = $this->extractProductPrice($crawler);

      return [
        'success' => true,
        'data' => [
          'name' => $name,
          'price' => $price,
          'asin' => $asin
        ]
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  private function extractProductName(Crawler $crawler): string
  {
    try {
      // Try different selectors for product title
      $selectors = [
        '#productTitle',
        '#title',
        'h1.product-title'
      ];

      foreach ($selectors as $selector) {
        $element = $crawler->filter($selector)->first();
        if ($element->count() > 0) {
          return trim($element->text());
        }
      }

      throw new Exception('Product name not found');
    } catch (Exception $e) {
      throw new Exception('Failed to extract product name: ' . $e->getMessage());
    }
  }

  private function extractProductPrice(Crawler $crawler): float
  {
    try {
      // Try different selectors for price
      $selectors = [
        '.a-price .a-offscreen',
        '#priceblock_ourprice',
        '#priceblock_dealprice',
        '.a-price-whole'
      ];

      foreach ($selectors as $selector) {
        try {
          $element = $crawler->filter($selector)->first();
          if ($element->count() > 0) {
            $price = $element->text();
            // Remove currency symbol and convert to float
            $price = preg_replace('/[^0-9.]/', '', $price);

            return (float) $price;
          }
        } catch (Exception $e) {
          continue;
        }
      }

      throw new Exception('Price not found');
    } catch (Exception $e) {
      throw new Exception('Failed to extract price: ' . $e->getMessage());
    }
  }
}
