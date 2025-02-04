<?php

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Controllers\AuthController;
use App\Controllers\CategoriesController;
use App\Controllers\DashboardController;
use App\Controllers\ProductsController;
use App\Database\Database;
use DI\Container;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create Container Builder
$containerBuilder = new ContainerBuilder();

// Add definitions
$containerBuilder->addDefinitions([
  'view' => function () {
    $templatesPath = dirname(__DIR__) . '/templates';
    if (!is_dir($templatesPath)) {
      throw new RuntimeException('Templates directory not found: ' . $templatesPath);
    }

    $renderer = new PhpRenderer($templatesPath);
    $renderer->setLayout('layout.php');

    return $renderer;
  },
  'db' => function () {
    return Database::getInstance()->getConnection();
  },
  AuthController::class => function (Container $container) {
    return new AuthController($container);
  },
  DashboardController::class => function (Container $container) {
    return new DashboardController($container);
  },
  App\Controllers\StoresController::class => function (Container $container) {
    return new App\Controllers\StoresController($container);
  },
  ProductsController::class => function (Container $container) {
    return new ProductsController($container);
  },
  CategoriesController::class => function (Container $container) {
    return new CategoriesController($container);
  }
]);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Register routes
require __DIR__ . '/../src/routes.php';

$app->run();
