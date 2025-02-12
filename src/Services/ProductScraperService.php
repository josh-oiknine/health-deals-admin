<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;
use Symfony\Component\DomCrawler\Crawler;

class ProductScraperService
{
  private Client $client;
  private const MAX_RETRIES = 3;
  private const RETRY_DELAY = 2; // seconds
  private const WALMART_GRAPHQL_ENDPOINT = 'https://www.walmart.com/orchestra/graphql';
  private array $userAgents = [
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36 Edg/119.0.0.0',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/115.0',
    'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5.2 Safari/605.1.15'
  ];

  public function __construct()
  {
    $this->initializeClient();
  }

  private function initializeClient(): void
  {
    $this->client = new Client([
      'timeout' => 30,
      'connect_timeout' => 10,
      'cookies' => true,
      'headers' => $this->getRandomHeaders(),
      RequestOptions::VERIFY => true,
      RequestOptions::ALLOW_REDIRECTS => [
        'max' => 5,
        'track_redirects' => true
      ],
      RequestOptions::HTTP_ERRORS => false
    ]);
  }

  // Headers
  private function getRandomHeaders(): array
  {
    $userAgent = $this->userAgents[array_rand($this->userAgents)];

    return [
      'User-Agent' => $userAgent,
      'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
      'Accept-Language' => 'en-US,en;q=0.9',
      'Accept-Encoding' => 'gzip, deflate, br',
      'Connection' => 'keep-alive',
      'Cache-Control' => 'max-age=0',
      'Sec-Ch-Ua' => '"Not_A Brand";v="8", "Chromium";v="120"',
      'Sec-Ch-Ua-Mobile' => '?0',
      'Sec-Ch-Ua-Platform' => '"Windows"',
      'Sec-Fetch-Dest' => 'document',
      'Sec-Fetch-Mode' => 'navigate',
      'Sec-Fetch-Site' => 'none',
      'Sec-Fetch-User' => '?1',
      'Upgrade-Insecure-Requests' => '1',
      'Referer' => 'https://www.google.com/'
    ];
  }
  private function getBaseHeaders(): array
  {
    $userAgent = $this->userAgents[array_rand($this->userAgents)];

    return [
      'User-Agent' => $userAgent,
      'Accept-Language' => 'en-US,en;q=0.9',
      'Accept-Encoding' => 'gzip, deflate, br',
    ];
  }
  private function getGraphQLHeaders(string $referer): array
  {
    return [
      'x-o-platform' => 'rweb',
      'x-o-bu' => 'WALMART-US',
      'x-o-mart' => 'B2C',
      'x-o-correlation-id' => bin2hex(random_bytes(16)),
      'sec-ch-ua-platform' => '"Windows"',
      'Origin' => 'https://www.walmart.com',
      'Referer' => $referer,
    ];
  }

  // Scrapers
  public function scrapeAmazonProduct(string $url): array
  {
    try {
      // Extract ASIN from URL
      preg_match('/\/dp\/([A-Z0-9]{10})/', $url, $matches);
      $asin = $matches[1] ?? null;

      if (!$asin) {
        throw new Exception('Invalid Amazon URL');
      }

      $crawler = $this->fetchPage($url);

      // Extract product information using Amazon-specific selectors
      $name = $this->extractText($crawler, [
        '#productTitle',
        '#title',
        'h1.product-title'
      ]);

      $price = $this->extractPrice($crawler, [
        '.a-price .a-offscreen',
        '#priceblock_ourprice',
        '#priceblock_dealprice',
        '.a-price-whole'
      ]);

      $description = $this->extractText($crawler, [
        '#productDescription',
        '#description',
        '.a-expander-content'
      ]);

      $image = $this->extractImage($crawler, [
        '#landingImage',
        '#image',
        '.a-dynamic-image img'
      ]);

      return [
        'success' => true,
        'data' => [
          'name' => $name,
          'price' => $price,
          'description' => $description,
          'image' => $image,
          'sku' => $asin,
          'store' => 'amazon'
        ]
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  public function scrapeWalmartProduct(string $url): array
  {
    try {
      // Improved ID extraction with fallback for different URL formats
      preg_match('/(?:\/ip\/.*?|\/product\/)(\d+)(?:[?\/]|$)/', $url, $matches);
      $itemId = $matches[1] ?? null;

      if (!$itemId || !ctype_digit($itemId)) {
        throw new Exception('Invalid Walmart URL - Missing numeric item ID');
      }

      $attempts = 0;
      $lastError = null;
      $client = new Client(['timeout' => 15]);

      while ($attempts < self::MAX_RETRIES) {
        try {
          // Get fresh cookies and headers each attempt
          $cookieJar = new CookieJar();
          $homepageResponse = $client->get('https://www.walmart.com/', [
            'cookies' => $cookieJar,
            'headers' => $this->getBaseHeaders()
          ]);

          // Improved hash extraction from JSON payload instead of regex
          $homepage = (string)$homepageResponse->getBody();
          if (!preg_match('/"__APOLLO_STATE__":(\{.*?\})\s*<\/script>/', $homepage, $jsonMatches)) {
            throw new Exception("Failed to extract Apollo state");
          }

          $apolloState = json_decode($jsonMatches[1], true);
          $queryHash = $apolloState['ROOT_QUERY']['__typename']['__r_hash'] ?? null;

          if (!$queryHash || strlen($queryHash) !== 64) {
            throw new Exception("Invalid GraphQL query hash");
          }

          // Updated GraphQL endpoint format
          $endpoint = "https://www.walmart.com/orchestration/pdp/graphql/v1/query";

          $response = $client->post($endpoint, [
            'cookies' => $cookieJar,
            'headers' => $this->getGraphQLHeaders($url),
            'json' => [
              'query' => 'query ItemByIdBtf'.$queryHash,
              'variables' => [
                'id' => $itemId,
                'persistedQuery' => [
                  'version' => 1,
                  'sha256Hash' => $queryHash
                ]
              ]
            ]
          ]);

          $data = json_decode($response->getBody(), true);

          // Handle GraphQL errors first
          if (!empty($data['errors'])) {
            throw new Exception("GraphQL Error: ".json_encode($data['errors']));
          }

          // Improved data extraction with fallbacks
          $product = $data['data']['product'] ?? [];
          if (empty($product)) {
            throw new Exception("Product data structure mismatch");
          }

          return [
            'success' => true,
            'data' => [
              'name' => $product['name'] ?? 'N/A',
              'price' => $this->extractWalmartPrice($product),
              'sku' => $itemId,
              'store' => 'walmart'
            ]
          ];

        } catch (Exception $e) {
          $lastError = $e;
          $attempts++;
          if ($attempts < self::MAX_RETRIES) {
            sleep(pow(2, $attempts)); // Exponential backoff
          }
        }
      }

      throw new Exception($lastError ? $lastError->getMessage() : 'Maximum retries exceeded');
    } catch (Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
      ];
    }
  }

  public function scrapeTargetProduct(string $url): array
  {
    try {
      // Extract TCIN from URL
      preg_match('/\/p\/([A-Z0-9]{10})/', $url, $matches);
      $tcin = $matches[1] ?? null;

      if (!$tcin || !ctype_digit($tcin)) {
        throw new Exception('Invalid Target URL - Missing numeric TCIN');
      }

      $crawler = $this->fetchPage($url);

      // Extract product information using Target-specific selectors
      $name = $this->extractText($crawler, [
        '#productTitle',
        '#title',
        'h1.product-title'
      ]);

      $price = $this->extractPrice($crawler, [
        '.price-display',
        '.price-display-group',
        '.price-display-group-with-savings'
      ]);

      $description = $this->extractText($crawler, [
        '#description',
        '.product-description-container'
      ]);

      $image = $this->extractImage($crawler, [
        '#image',
        '.product-image'
      ]);

      return [
        'success' => true,
        'data' => [
          'name' => $name,
          'price' => $price,
          'sku' => $tcin,
          'store' => 'target'
        ]
      ];
    } catch (Exception $e) {
      return [
        'success' => false,
        'error' => $e->getMessage()
      ];
    }
  }

  // Common
  private function fetchPage(string $url): Crawler
  {
    $response = $this->client->get($url);
    $statusCode = $response->getStatusCode();

    if ($statusCode !== 200) {
      throw new Exception("Failed to fetch page. Status code: {$statusCode}");
    }

    $html = (string) $response->getBody();

    if (empty($html)) {
      throw new Exception('Received empty response from server');
    }

    // Log the first 500 characters of the response for debugging
    error_log("Response preview: " . substr($html, 0, 500));

    return new Crawler($html);
  }

  public static function extractSkuFromUrl(string $url): string
  {
    if (strpos($url, 'amazon.com') !== false) {
      preg_match('/\/dp\/([A-Z0-9]{10})/', $url, $matches);

      return $matches[1] ?? '';
    } elseif (strpos($url, 'walmart.com') !== false) {
      preg_match('/(?:\/ip\/.*?|\/product\/)(\d+)(?:[?\/]|$)/', $url, $matches);

      return $matches[1] ?? '';
    } elseif (strpos($url, 'target.com') !== false) {
      preg_match('/\/p\/([A-Z0-9]{10})/', $url, $matches);

      return $matches[1] ?? '';
    }

    return '';
  }

  // Amazon
  private function extractText(Crawler $crawler, array $selectors): string
  {
    try {
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
  private function extractPrice(Crawler $crawler, array $selectors): float
  {
    try {
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
  private function extractImage(Crawler $crawler, array $selectors): string
  {
    try {
      foreach ($selectors as $selector) {
        $element = $crawler->filter($selector)->first();
        if ($element->count() > 0) {
          return $element->attr('src');
        }
      }

      throw new Exception('Image not found');
    } catch (Exception $e) {
      throw new Exception('Failed to extract image: ' . $e->getMessage());
    }
  }

  // Walmart
  private function extractWalmartPrice(array $product): float
  {
    return $product['priceInfo']['currentPrice']['price']
      ?? $product['priceInfo']['wasPrice']['price']
      ?? $product['priceInfo']['priceRange']['minPrice']
      ?? 0.0;
  }
}
