<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Product;
use App\Models\Store;
use Slim\Exception\HttpNotFoundException;

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
        $stores = Store::findAll();
        $error = null;

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            error_log("Product data received: " . print_r($data, true));
            
            $product = new Product(
                (int)($data['store_id'] ?? 0),
                $data['name'] ?? '',
                $data['description'] ?? '',
                (float)($data['price'] ?? 0.0),
                !empty($data['sale_price']) ? (float)$data['sale_price'] : null,
                $data['image_url'] ?? null,
                $data['product_url'] ?? '',
                ($data['is_active'] ?? '') === 'on'
            );

            if ($product->save()) {
                return $response->withHeader('Location', '/products')
                               ->withStatus(302);
            }
            error_log("Failed to save product");
            $error = "Failed to save product. Please try again.";
        }

        return $this->view->render($response, 'products/form.php', [
            'product' => new Product(),
            'stores' => $stores,
            'isEdit' => false,
            'error' => $error
        ]);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $product = Product::findById((int)$args['id']);
        if (!$product) {
            throw new HttpNotFoundException($request);
        }

        $stores = Store::findAll();
        $error = null;

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            error_log("Product edit data received: " . print_r($data, true));
            
            $product->setStoreId((int)($data['store_id'] ?? 0));
            $product->setName($data['name'] ?? '');
            $product->setDescription($data['description'] ?? '');
            $product->setPrice((float)($data['price'] ?? 0.0));
            $product->setSalePrice(!empty($data['sale_price']) ? (float)$data['sale_price'] : null);
            $product->setImageUrl($data['image_url'] ?? null);
            $product->setProductUrl($data['product_url'] ?? '');
            $product->setIsActive(($data['is_active'] ?? '') === 'on');

            if ($product->save()) {
                return $response->withHeader('Location', '/products')
                               ->withStatus(302);
            }
            error_log("Failed to update product");
            $error = "Failed to update product. Please try again.";
        }

        return $this->view->render($response, 'products/form.php', [
            'product' => $product,
            'stores' => $stores,
            'isEdit' => true,
            'error' => $error
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $product = Product::findById((int)$args['id']);
        if ($product) {
            $product->softDelete();
        }
        return $response->withHeader('Location', '/products')
                       ->withStatus(302);
    }

    public function byStore(Request $request, Response $response, array $args): Response
    {
        $store = Store::findById((int)$args['store_id']);
        if (!$store) {
            throw new HttpNotFoundException($request);
        }

        $products = Product::findByStoreId($store->getId());
        return $this->view->render($response, 'products/by-store.php', [
            'products' => $products,
            'store' => $store
        ]);
    }
} 