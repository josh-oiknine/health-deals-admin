<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Store;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class ProductsController
{
    private $view;

    public function __construct($container)
    {
        $this->view = $container->get('view');
    }

    public function index(Request $request, Response $response): Response
    {
        $products = Product::findAll();
        return $this->view->render($response, 'products/index.php', [
            'products' => $products
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        $stores = Store::findAllActive();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            $product = new Product(
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['url'] ?? '',
                isset($data['category_id']) ? (int)$data['category_id'] : null,
                (int)($data['store_id'] ?? 0),
                (float)($data['regular_price'] ?? 0.0),
                $data['sku'] ?? null,
                isset($data['is_active'])
            );

            if ($product->save()) {
                return $response->withHeader('Location', '/products')
                               ->withStatus(302);
            }
        }

        return $this->view->render($response, 'products/form.php', [
            'product' => null,
            'stores' => $stores,
            'mode' => 'add'
        ]);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $productData = Product::findById($id);
        
        if (!$productData) {
            return $response->withHeader('Location', '/products')
                           ->withStatus(302);
        }

        $stores = Store::findAllActive();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            
            $product = new Product(
                $data['name'] ?? '',
                $data['slug'] ?? '',
                $data['url'] ?? '',
                isset($data['category_id']) ? (int)$data['category_id'] : null,
                (int)($data['store_id'] ?? 0),
                (float)($data['regular_price'] ?? 0.0),
                $data['sku'] ?? null,
                isset($data['is_active'])
            );
            $product->setId($id);

            if ($product->save()) {
                return $response->withHeader('Location', '/products')
                               ->withStatus(302);
            }
        }

        return $this->view->render($response, 'products/form.php', [
            'product' => $productData,
            'stores' => $stores,
            'mode' => 'edit'
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        Product::delete($id);

        return $response->withHeader('Location', '/products')
                       ->withStatus(302);
    }
}
