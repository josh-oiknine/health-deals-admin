<?php

use App\Controllers\Api\DealsController as ApiDealsController;
use App\Controllers\Api\ProductsController as ApiProductsController;
use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\DashboardController;
use App\Controllers\DealsController;
use App\Controllers\ProductsController;
use App\Controllers\ScrapingJobsController;
use App\Controllers\SettingsController;
use App\Controllers\StoresController;
use App\Controllers\UsersController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

// Public routes
$app->group('', function (RouteCollectorProxy $group) {
  $group->get('/', [AuthController::class, 'loginPage'])->setName('login');
  $group->post('/login', [AuthController::class, 'login']);
  $group->get('/setup-2fa', [AuthController::class, 'setup2faPage']);
  $group->post('/setup-2fa', [AuthController::class, 'setup2fa']);
  $group->get('/mfa', [AuthController::class, 'mfaPage']);
  $group->post('/verify-mfa', [AuthController::class, 'verifyMfa']);

  // Extension API routes
  $group->options('/api/login', [AuthController::class, 'handleOptionsRequest']);
  $group->post('/api/login', [AuthController::class, 'apiLogin']);
  $group->options('/api/verify-token', [AuthController::class, 'handleOptionsRequest']);
  $group->get('/api/verify-token', [AuthController::class, 'apiVerifyToken']);

  // $group->get('/test', [ProductsController::class, 'testStuff']);
});

// Protected routes
$app->group('', function (RouteCollectorProxy $group) {
  $group->get('/dashboard', [DashboardController::class, 'index'])->setName('dashboard');

  // Users routes
  $group->get('/users', [UsersController::class, 'index'])->setName('users');
  $group->get('/users/add', [UsersController::class, 'add']);
  $group->post('/users/add', [UsersController::class, 'add']);
  $group->get('/users/edit/{id}', [UsersController::class, 'edit']);
  $group->post('/users/edit/{id}', [UsersController::class, 'edit']);
  $group->post('/users/delete/{id}', [UsersController::class, 'delete']);
  $group->post('/users/remove-mfa/{id}', [UsersController::class, 'removeMfa']);

  // Stores routes
  $group->get('/stores', [StoresController::class, 'index'])->setName('stores');
  $group->get('/stores/add', [StoresController::class, 'add']);
  $group->post('/stores/add', [StoresController::class, 'add']);
  $group->get('/stores/edit/{id}', [StoresController::class, 'edit']);
  $group->post('/stores/edit/{id}', [StoresController::class, 'edit']);
  $group->post('/stores/delete/{id}', [StoresController::class, 'delete']);

  // Category routes
  $group->get('/categories', [CategoriesController::class, 'index'])->setName('categories');
  $group->get('/categories/add', [CategoriesController::class, 'add']);
  $group->post('/categories/add', [CategoriesController::class, 'add']);
  $group->get('/categories/edit/{id}', [CategoriesController::class, 'edit']);
  $group->post('/categories/edit/{id}', [CategoriesController::class, 'edit']);

  // Product routes
  $group->get('/products', [ProductsController::class, 'index'])->setName('products');
  $group->get('/products/add', [ProductsController::class, 'add']);
  $group->post('/products/add', [ProductsController::class, 'add']);
  $group->get('/products/edit/{id}', [ProductsController::class, 'edit']);
  $group->post('/products/edit/{id}', [ProductsController::class, 'edit']);
  $group->post('/products/delete/{id}', [ProductsController::class, 'delete']);
  $group->get('/products/history/{id}', [ProductsController::class, 'history']);

  $group->post('/api/products/add', [ProductsController::class, 'apiAdd']);
  $group->get('/api/products/find', [ProductsController::class, 'apiFind']);
  $group->post('/api/products/update-price', [ProductsController::class, 'apiUpdatePrice']);

  // Scraping Jobs routes
  $group->get('/scraping-jobs', [ScrapingJobsController::class, 'index'])->setName('scraping-jobs');
  $group->post('/scraping-jobs/add', [ScrapingJobsController::class, 'add']);
  $group->post('/scraping-jobs/stop/{id}', [ScrapingJobsController::class, 'stop']);

  // Deal routes
  $group->get('/deals', [DealsController::class, 'index'])->setName('deals');
  $group->get('/deals/add', [DealsController::class, 'add']);
  $group->post('/deals/add', [DealsController::class, 'add']);
  $group->get('/deals/edit/{id}', [DealsController::class, 'edit']);
  $group->post('/deals/edit/{id}', [DealsController::class, 'edit']);
  $group->post('/deals/delete/{id}', [DealsController::class, 'delete']);

  // Settings routes
  $group->get('/settings', [SettingsController::class, 'index'])->setName('settings');
  $group->post('/settings/change-password', [SettingsController::class, 'changePassword']);
  $group->get('/settings/change-mfa', [SettingsController::class, 'changeMfaDevice']);
  $group->post('/settings/verify-mfa', [SettingsController::class, 'verifyNewMfaDevice']);

  // API routes
  $group->get('/api/products/fetch-info', [ApiProductsController::class, 'fetchInfo']);
  $group->get('/api/deals/fetch-info', [ApiDealsController::class, 'fetchInfo']);

  // Placeholder routes for future implementation
  $group->get('/outbox', function ($request, $response) {
    return $response->withHeader('Location', '/dashboard');
  });

  $group->post('/logout', [AuthController::class, 'logout'])->setName('logout');
})->add(new AuthMiddleware());
