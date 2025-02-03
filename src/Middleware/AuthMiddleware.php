<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $response = new Response();
        $token = $_COOKIE['auth_token'] ?? null;

        // Debug: Log the current route
        error_log('AuthMiddleware: Checking auth for path: ' . $request->getUri()->getPath());
        
        // Debug: Check if token exists
        if (!$token) {
            error_log('AuthMiddleware: No auth_token cookie found');
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        error_log('AuthMiddleware: Token found, attempting to decode');

        try {
            // Debug: Log JWT secret length to ensure it's set (don't log the actual secret)
            $jwtSecret = $_ENV['JWT_SECRET'] ?? null;
            if (!$jwtSecret) {
                error_log('AuthMiddleware: JWT_SECRET is not set in environment variables');
                throw new \Exception('JWT_SECRET not configured');
            }
            error_log('AuthMiddleware: JWT_SECRET length: ' . strlen($jwtSecret));

            // Attempt to decode the token
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            
            // Debug: Log successful decode
            error_log('AuthMiddleware: Token decoded successfully. User ID: ' . $decoded->user_id);
            
            // Check token expiration explicitly
            if (isset($decoded->exp) && $decoded->exp < time()) {
                error_log('AuthMiddleware: Token has expired. Expiry: ' . date('Y-m-d H:i:s', $decoded->exp));
                throw new \Exception('Token expired');
            }

            $request = $request->withAttribute('user_id', $decoded->user_id);
            return $handler->handle($request);

        } catch (\Exception $e) {
            // Debug: Log the specific error
            error_log('AuthMiddleware: Token validation failed - ' . $e->getMessage());
            error_log('AuthMiddleware: Exception trace - ' . $e->getTraceAsString());

            // Clear the invalid token
            setcookie('auth_token', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }
    }
} 