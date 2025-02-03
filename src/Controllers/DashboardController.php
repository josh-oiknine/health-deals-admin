<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Store;

class DashboardController
{
    private $view;

    public function __construct($container)
    {
        $this->view = $container->get('view');
    }

    public function index(Request $request, Response $response): Response
    {
        $metrics = [
            'activeStores' => Store::countActive(),
            // Prepare for future metrics
            'activeProducts' => 0, // TODO: Implement when Products model is ready
            'activeCategories' => 0, // TODO: Implement when Categories model is ready
            'messagesSentToday' => 0, // TODO: Implement when Outbox model is ready
        ];

        return $this->view->render($response, 'dashboard/index.php', [
            'metrics' => $metrics
        ]);
    }
} 