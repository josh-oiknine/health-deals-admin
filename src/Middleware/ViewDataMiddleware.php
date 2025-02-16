<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\PhpRenderer;

class ViewDataMiddleware
{
    private $view;

    public function __construct(PhpRenderer $view)
    {
        $this->view = $view;
    }

    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        // Get current user info from JWT token
        $currentUserEmail = null;
        $authToken = $_COOKIE['auth_token'] ?? null;

        if ($authToken) {
            try {
                $decoded = JWT::decode($authToken, new Key($_ENV['JWT_SECRET'], 'HS256'));
                $currentUserEmail = $decoded->email ?? null;
                $currentUserId = $decoded->id ?? null;
            } catch (\Exception $e) {
                // Token is invalid
            }
        }

        // Set up view data
        $this->view->addAttribute('currentUserEmail', $currentUserEmail);
        $this->view->addAttribute('currentUserId', $currentUserId);
        $this->view->addAttribute('isLoginPage', $_SERVER['REQUEST_URI'] === '/');
        $this->view->addAttribute('isMFAPage', $_SERVER['REQUEST_URI'] === '/mfa');
        $this->view->addAttribute('isSetup2FAPage', $_SERVER['REQUEST_URI'] === '/setup-2fa');
        $this->view->addAttribute('isVerifyMFAPage', $_SERVER['REQUEST_URI'] === '/verify-mfa');
        $this->view->addAttribute('hasAuthToken', isset($_COOKIE['auth_token']));

        return $handler->handle($request);
    }
} 