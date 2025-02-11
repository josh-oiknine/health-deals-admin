<?php

session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProductsController;
use App\Controllers\DealsController;
use App\Controllers\CategoriesController;
use App\Controllers\StoresController;
use App\Controllers\SettingsController;
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
  'redis' => function () {
    return new Redis();
  },
  AuthController::class => function (Container $container) {
    return new AuthController($container);
  },
  DashboardController::class => function (Container $container) {
    return new DashboardController($container);
  },
  ProductsController::class => function (Container $container) {
    return new ProductsController($container);
  },
  DealsController::class => function (Container $container) {
    return new DealsController($container);
  },
  CategoriesController::class => function (Container $container) {
    return new CategoriesController($container);
  },
  StoresController::class => function (Container $container) {
    return new StoresController($container);
  },
  SettingsController::class => function (Container $container) {
    return new SettingsController($container);
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
