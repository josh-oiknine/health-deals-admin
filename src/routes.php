<?php

use App\Controllers\Api\ProductsController as ApiProductsController;
use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\DashboardController;
use App\Controllers\ProductsController;
use App\Controllers\StoresController;
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

  $group->post('/api/products/add', [ProductsController::class, 'apiAdd']);

  // API routes
  $group->get('/api/products/fetch-info', [ApiProductsController::class, 'fetchInfo']);

  // Placeholder routes for future implementation
  $group->get('/deals', function ($request, $response) {
    return $response->withHeader('Location', '/dashboard');
  });
  $group->get('/outbox', function ($request, $response) {
    return $response->withHeader('Location', '/dashboard');
  });

  $group->post('/logout', [AuthController::class, 'logout'])->setName('logout');
})->add(new AuthMiddleware());
