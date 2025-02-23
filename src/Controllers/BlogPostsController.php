<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BlogPost;
use App\Models\User;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogPostsController
{
  private $view;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php');
  }

  public function index(Request $request, Response $response): Response
  {
    $queryParams = $request->getQueryParams();
    $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
    $perPage = isset($queryParams['per_page']) ? (int)$queryParams['per_page'] : 20;
    $sortBy = $queryParams['sort_by'] ?? 'created_at';
    $sortOrder = $queryParams['sort_order'] ?? 'DESC';

    $filters = [
      'keyword' => $queryParams['keyword'] ?? '',
      'is_published' => $queryParams['is_published'] ?? null,
      'user_id' => $queryParams['user_id'] ?? null
    ];

    $blogPosts = BlogPost::findFiltered($filters, $sortBy, $sortOrder, $page, $perPage);
    $users = User::findAll();

    return $this->view->render($response, 'blog-posts/index.php', [
      'title' => 'Blog Posts',
      'blogPosts' => $blogPosts['data'],
      'pagination' => [
        'current_page' => $blogPosts['page'],
        'per_page' => $blogPosts['per_page'],
        'total' => $blogPosts['total'],
        'last_page' => $blogPosts['last_page']
      ],
      'filters' => $filters,
      'sorting' => [
        'sort_by' => $sortBy,
        'sort_order' => $sortOrder
      ],
      'users' => $users,
      'currentUserEmail' => $this->getCurrentUserEmail($request)
    ]);
  }

  public function view(Request $request, Response $response, array $args): Response
  {
    $blogPost = BlogPost::findById((int)$args['id']);

    // Don't use layout for this view
    $this->view->setLayout('');

    return $this->view->render(
      $response,
      'blog-posts/view.php',
      [
        'blogPost' => $blogPost
      ]
    );
  }

  public function add(Request $request, Response $response): Response
  {
    $error = null;
    $blogPostData = [];

    if ($request->getMethod() === 'POST') {
      try {
        $blogPostData = $request->getParsedBody();

        $blogPost = new BlogPost(
          $blogPostData['title'] ?? '',
          $blogPostData['slug'] ?? '',
          $blogPostData['body'] ?? '',
          $blogPostData['seo_keywords'] ?? null,
          isset($blogPostData['published_at']) ? new DateTime($blogPostData['published_at']) : null,
          (int)$blogPostData['user_id']
        );

        if ($blogPost->save()) {
          return $response->withHeader('Location', '/blog-posts')
            ->withStatus(302);
        }
        $error = "Failed to save blog post. Please try again.";
      } catch (Exception $e) {
        error_log("Error in BlogPostsController::add(): " . $e->getMessage());
        $error = $e->getMessage();
      }
    }

    $users = User::findAll();

    return $this->view->render($response, 'blog-posts/form.php', [
      'title' => 'Add Blog Post',
      'blogPost' => $blogPostData,
      'isEdit' => false,
      'error' => $error,
      'currentUserEmail' => $this->getCurrentUserEmail($request),
      'users' => $users
    ]);
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    $id = (int)$args['id'];
    $error = null;

    if ($request->getMethod() === 'POST') {
      try {
        $blogPostData = $request->getParsedBody();

        $blogPost = new BlogPost(
          $blogPostData['title'] ?? '',
          $blogPostData['slug'] ?? '',
          $blogPostData['body'] ?? '',
          $blogPostData['seo_keywords'] ?? null,
          isset($blogPostData['published_at']) ? new DateTime($blogPostData['published_at']) : null,
          (int)$blogPostData['user_id']
        );
        $blogPost->setId($id);

        if ($blogPost->save()) {
          return $response->withHeader('Location', '/blog-posts')
            ->withStatus(302);
        }
        $error = "Failed to update blog post. Please try again.";
      } catch (Exception $e) {
        error_log("Error in BlogPostsController::edit(): " . $e->getMessage());
        $error = $e->getMessage();
      }
    } else {
      $blogPostData = BlogPost::findById($id);
    }

    if (!$blogPostData) {
      return $response->withHeader('Location', '/blog-posts')
        ->withStatus(302);
    }

    $users = User::findAll();

    return $this->view->render($response, 'blog-posts/form.php', [
      'title' => 'Edit Blog Post',
      'blogPost' => $blogPostData,
      'isEdit' => true,
      'error' => $error,
      'currentUserEmail' => $this->getCurrentUserEmail($request),
      'users' => $users
    ]);
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    $blogPost = new BlogPost();
    $blogPost->setId((int)$args['id']);
    $blogPost->softDelete();

    return $response->withHeader('Location', '/blog-posts')
      ->withStatus(302);
  }

  private function getCurrentUserEmail(Request $request): ?string
  {
    return $this->view->getAttribute('currentUserEmail');
  }
}
