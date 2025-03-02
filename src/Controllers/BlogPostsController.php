<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BlogPost;
use App\Models\User;
use App\Services\S3Service;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class BlogPostsController
{
  private $view;
  private $s3Service;

  public function __construct($container)
  {
    $this->view = $container->get('view');
    $this->view->setLayout('layout.php');
    $this->s3Service = new S3Service();
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
        $uploadedFiles = $request->getUploadedFiles();
        
        // Handle featured image upload
        $featuredImageUrl = null;
        if (isset($uploadedFiles['featured_image']) && $uploadedFiles['featured_image']->getError() === UPLOAD_ERR_OK) {
          $featuredImageUrl = $this->handleImageUpload($uploadedFiles['featured_image']);
          if (!$featuredImageUrl) {
            throw new Exception('Failed to upload featured image');
          }
        }

        // Handle published_at based on checkbox state and convert local time to UTC
        $publishedAt = null;
        if (isset($blogPostData['is_published']) && !empty($blogPostData['published_at'])) {
            // Convert local time to UTC
            $localTime = new DateTime($blogPostData['published_at']);
            $localTime->setTimezone(new \DateTimeZone('UTC'));
            $publishedAt = $localTime;
        }

        $blogPost = new BlogPost(
          $blogPostData['title'] ?? '',
          $blogPostData['slug'] ?? '',
          $blogPostData['body'] ?? '',
          $blogPostData['seo_keywords'] ?? null,
          $publishedAt,
          (int)$blogPostData['user_id'],
          $featuredImageUrl
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
        $uploadedFiles = $request->getUploadedFiles();
        
        // Get the existing blog post to check if we need to update the image
        $existingBlogPost = BlogPost::findById($id);
        
        // Handle featured image upload
        $featuredImageUrl = $existingBlogPost['featured_image_url'] ?? null;
        if (isset($uploadedFiles['featured_image']) && $uploadedFiles['featured_image']->getError() === UPLOAD_ERR_OK) {
          // Upload new image
          $newImageUrl = $this->handleImageUpload($uploadedFiles['featured_image']);
          if (!$newImageUrl) {
            throw new Exception('Failed to upload featured image');
          }
          
          // Delete old image if it exists
          if ($featuredImageUrl) {
            $this->s3Service->deleteFile($featuredImageUrl);
          }
          
          $featuredImageUrl = $newImageUrl;
        } elseif (isset($blogPostData['remove_featured_image']) && $blogPostData['remove_featured_image'] === '1') {
          // User wants to remove the image
          if ($featuredImageUrl) {
            $this->s3Service->deleteFile($featuredImageUrl);
          }
          $featuredImageUrl = null;
        }

        // Handle published_at based on checkbox state and convert local time to UTC
        $publishedAt = null;
        if (isset($blogPostData['is_published']) && !empty($blogPostData['published_at'])) {
            // Convert local time to UTC
            $localTime = new DateTime($blogPostData['published_at']);
            $localTime->setTimezone(new \DateTimeZone('UTC'));
            $publishedAt = $localTime;
        }

        $blogPost = new BlogPost(
          $blogPostData['title'] ?? '',
          $blogPostData['slug'] ?? '',
          $blogPostData['body'] ?? '',
          $blogPostData['seo_keywords'] ?? null,
          $publishedAt,
          (int)$blogPostData['user_id'],
          $featuredImageUrl
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

  /**
   * Handle image upload to S3
   * 
   * @param UploadedFileInterface $uploadedFile The uploaded file
   * @return string|null The URL of the uploaded file or null on failure
   */
  private function handleImageUpload(UploadedFileInterface $uploadedFile): ?string
  {
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($uploadedFile->getClientMediaType(), $allowedTypes)) {
      error_log("Invalid file type: " . $uploadedFile->getClientMediaType());
      return null;
    }
    
    // Validate file size (max 5MB)
    if ($uploadedFile->getSize() > 5 * 1024 * 1024) {
      error_log("File too large: " . $uploadedFile->getSize());
      return null;
    }
    
    // Upload to S3
    return $this->s3Service->uploadFile($uploadedFile, 'blog-images');
  }
}
