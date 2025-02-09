<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GuzzleHttp\Client;

class ProductsController
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
        ? ($queryParams['category_id'] === 'none' ? 0 : (int)$queryParams['category_id'])
        : null
    ];
    
    // Handle active/inactive filter
    if (isset($queryParams['is_active']) && $queryParams['is_active'] !== '') {
      $filters['is_active'] = $queryParams['is_active'] === '1';
    }
    
    // Get sorting parameters
    $sortBy = $queryParams['sort_by'] ?? 'created_at';
    $sortOrder = strtoupper($queryParams['sort_order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    
    // Get filtered products
    $result = Product::findFiltered($filters, $sortBy, $sortOrder, $page, $perPage);
    
    // Get all stores and categories for filters
    $stores = Store::findAllActive();
    $categories = Category::findAllActive();
    
    return $this->view->render($response, 'products/index.php', [
      'products' => $result['data'],
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

        // Log the incoming data
        error_log("Received form data: " . json_encode($data));

        $product = new Product(
          $data['name'] ?? '',
          $data['slug'] ?? '',
          $data['url'] ?? '',
          !empty($data['category_id']) ? (int)$data['category_id'] : null,
          (int)($data['store_id'] ?? 0),
          (float)($data['regular_price'] ?? 0.0),
          $data['sku'] ?? null,
          isset($data['is_active']) && $data['is_active'] === 'on'
        );

        // Check for duplicates SKU
        $sku = $data['sku'] ?? null;
        if ($sku) {
          $existingProduct = Product::findBySku($sku);
          if ($existingProduct) {
            $error = 'Duplicate SKU. Please use a unique SKU.';
            return $this->view->render($response, 'products/form.php', [
              'product' => $product,
              'stores' => $stores,
              'categories' => $categories,
              'mode' => 'add',
              'error' => $error
            ]);
          }
        }

        if ($product->save()) {
          return $response->withHeader('Location', '/products')
            ->withStatus(302);
        } else {
          $error = 'Failed to save the product. Please try again.';
        }
      } catch (Exception $e) {
        error_log("Error in ProductsController::add(): " . $e->getMessage());
        $error = 'An error occurred while saving the product.';
      }
    }

    return $this->view->render($response, 'products/form.php', [
      'product' => null,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'add',
      'error' => $error
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $productData = Product::findById($id);
    $error = null;

    if (!$productData) {
      return $response->withHeader('Location', '/products')
        ->withStatus(302);
    }

    $stores = Store::findAllActive();
    $categories = Category::findAllActive();

    if ($request->getMethod() === 'POST') {
      try {
        $data = $request->getParsedBody();

        // Log the incoming data
        error_log("Received form data for edit: " . json_encode($data));

        $product = new Product(
          $data['name'] ?? '',
          $data['slug'] ?? '',
          $data['url'] ?? '',
          !empty($data['category_id']) ? (int)$data['category_id'] : null,
          (int)($data['store_id'] ?? 0),
          (float)($data['regular_price'] ?? 0.0),
          $data['sku'] ?? null,
          isset($data['is_active']) && $data['is_active'] === 'on'
        );
        $product->setId($id);

        if ($product->save()) {
          return $response->withHeader('Location', '/products')
            ->withStatus(302);
        } else {
          $error = 'Failed to update the product. Please try again.';
        }
      } catch (Exception $e) {
        error_log("Error in ProductsController::edit(): " . $e->getMessage());
        $error = 'An error occurred while updating the product.';
      }
    }

    return $this->view->render($response, 'products/form.php', [
      'product' => $productData,
      'stores' => $stores,
      'categories' => $categories,
      'mode' => 'edit',
      'error' => $error
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    Product::delete($id);

    return $response->withHeader('Location', '/products')
      ->withStatus(302);
  }

  // API FUNCTIONS
  public function apiAdd(Request $request, Response $response): Response
  {
    // Add CORS headers
    $response = $response
      ->withHeader('Access-Control-Allow-Origin', $_ENV['APP_URL'] ?? '*')
      ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
      ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
      ->withHeader('Access-Control-Allow-Credentials', 'true')
      ->withHeader('Content-Type', 'application/json');

    // POST DATA
    $data = $request->getParsedBody();
    $name = $data['product_name'] ?? '';
    $url = $data['url'] ?? '';
    $regularPrice = $data['regular_price'] ?? 0.0;
    $sku = $data['sku'] ?? null;
    
    // JSON DATA
    if (empty($name) || empty($url) || empty($regularPrice) || empty($sku)) {
      $rawBody = $request->getBody()->__toString();
      $data = json_decode($rawBody, true);
      $name = $data['product_name'] ?? '';
      $url = $data['url'] ?? '';
      $regularPrice = $data['regular_price'] ?? 0.0;
      $sku = $data['sku'] ?? null;
    }

    if (empty($name) || empty($url) || empty($regularPrice) || empty($sku)) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Missing required fields'
      ]));
      return $response->withStatus(400);
    }

    // Check for duplicates SKU
    $existingProduct = Product::findBySku($sku);
    if ($existingProduct) {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Duplicate SKU. Please use a unique SKU.'
      ]));
      return $response->withStatus(400);
    }

    // Find the store
    $parsedUrl = parse_url($url);
    $domain = $parsedUrl['host'] ?? '';
    $domain_wo = str_replace('www.', '', $domain);

    $storeId = null;
    $store = Store::findByDomain($domain_wo);
    if ($store) {
      $storeId = $store['id'];
    } else {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Store ' . $domain . ' not found'
      ]));
      return $response->withStatus(400);
    }

    $slug = self::makeSlug($name);

    // if I've made it here then I want to add in some AI to decide what the category should be based on the Product Name and/or URL
    $category = self::decideCategory($name, $url);

    $product = new Product(
      $name,
      $slug,
      $url,
      $category, // category_id
      $storeId,
      (float)$regularPrice,
      $sku,
      true // is_active
    );

    if ($product->save()) {
      $response->getBody()->write(json_encode([
        'status' => 'success',
        'message' => 'Product added successfully'
      ]));

      return $response->withStatus(200);
    } else {
      $response->getBody()->write(json_encode([
        'status' => 'error',
        'message' => 'Failed to save the product. Please try again.'
      ]));
      return $response->withStatus(400);
    }

    $response->getBody()->write(json_encode([
      'status' => 'error',
      'message' => 'Failed to save the product. Please try again.'
    ]));
    return $response->withStatus(400);
  }

  private static function makeSlug($name, $maxLength = 100)
  {
    $slug = strtolower($name);
    $slug = preg_replace('/[^a-z0-9]+/', '_', $slug);
    $slug = preg_replace('/^_|_$/', '', $slug);
      
    // If slug is longer than maxLength, trim it at the last underscore before maxLength
    if (strlen($slug) > $maxLength) {
      $slug = substr($slug, 0, $maxLength);
      // Find the last underscore in the truncated string
      $lastUnderscore = strrpos($slug, '_');
      if ($lastUnderscore !== false) {
        $slug = substr($slug, 0, $lastUnderscore);
      }
    }
    
    return $slug;
  }

  public function testStuff(Request $request, Response $response): Response
  {
    // $category = self::decideCategory('Chosen Foods 100% Pure Avocado Oil - Size 33.8oz', 'https://www.target.com/p/chosen-foods-100-pure-avocado-oil/-/A-89461892?preselect=88368709#lnk=sametab');
    
    // return $response->withHeader('application/json', json_encode([
    //   'status' => 'success',
    //   'message' => 'Category: ' . $category
    // ]));
  }

  private static function decideCategory($name, $url)
  {
    try {
      $apiKey = $_ENV['GEMINI_API_KEY'];
      
      if (empty($apiKey)) {
        error_log('Gemini API key is not configured');
        return 'Uncategorized';
      }

      $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

      $categories = Category::findAllActive();
      if (empty($categories)) {
        error_log('No categories found in the database');
        return 'Uncategorized';
      }

      $categoryNames = array_map(function($category) {
        return $category['name'];
      }, $categories);
      $listOfCategories = implode(', ', $categoryNames);
    
      // $promptTemplate = "
      //   You are a product categorization expert. Given a product name, URL, and list of available categories, analyze the product and return ONLY the
      //   single most appropriate category name from the provided list. Do not include any explanation, commentary, or additional text - just output the
      //   exact category name.

      //   Product Details:
      //   Name: {product_name}
      //   URL: {product_url}

      //   Available Categories:
      //   {list_of_categories}

      //   Rules:

      //   Return only the exact category name from the provided list
      //   Do not add any additional text or explanation
      //   If no categories fit well, return \"Uncategorized\"
      //   Match based on product name and URL content
      //   Consider both specific and general category matches
      //   Choose the most specific applicable category
      // ";

      $promptTemplate = "Given the product name: '{product_name}' and URL: '{product_url}', select the most appropriate category from this list: '{list_of_categories}'. Respond with only the category name, nothing else.";

      $prompt = str_replace('{product_name}', $name, $promptTemplate);
      $prompt = str_replace('{product_url}', $url, $prompt);
      $prompt = str_replace('{list_of_categories}', $listOfCategories, $prompt);

      $data = [
        'contents' => [
          [
            'parts' => [
              ['text' => $prompt]
            ]
          ]
        ],
        'generationConfig' => [
          'temperature' => 0.3,
          'maxOutputTokens' => 50,
        ]
      ];

      $client = new Client([
        'timeout' => 10, // 10 second timeout
        'http_errors' => false // Don't throw exceptions for HTTP errors
      ]);

      $response = $client->post($url . '?key=' . $apiKey, [
        'headers' => [
          'Content-Type' => 'application/json'
        ],
        'json' => $data
      ]);

      $statusCode = $response->getStatusCode();
      
      if ($statusCode !== 200) {
        error_log("Gemini API request failed with status code: {$statusCode}");
        error_log("Response body: " . $response->getBody()->getContents());
        return 'Uncategorized';
      }

      $responseData = json_decode($response->getBody()->getContents(), true);
      
      if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Failed to parse Gemini API response: ' . json_last_error_msg());
        return 'Uncategorized';
      }

      $aiCategoryName = 'Uncategorized';
      if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $aiCategoryName = trim($responseData['candidates'][0]['content']['parts'][0]['text']);
      }

      error_log('AI Found Category Name: ' . $aiCategoryName);

      // Find the ID of the category from the $categories array
      $categoryId = null;
      foreach ($categories as $category) {
        if ($category['name'] === $aiCategoryName) {
          $categoryId = $category['id'];
          break;
        }
      }
      
      return $categoryId;

    } catch (Exception $e) {
      error_log('Error in decideCategory: ' . $e->getMessage());
      return 'Uncategorized';
    }
  }
}
