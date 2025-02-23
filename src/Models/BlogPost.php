<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

class BlogPost
{
  private $id = null;
  private $title;
  private $slug;
  private $body;
  private $seo_keywords;
  private $user_id;
  private ?DateTime $created_at = null;
  private ?DateTime $updated_at = null;
  private ?DateTime $published_at = null;
  private ?DateTime $deleted_at = null;

  public function __construct(
    string $title = '',
    string $slug = '',
    string $body = '',
    ?string $seo_keywords = null,
    ?DateTime $published_at = null,
    int $user_id = 0
  ) {
    $this->title = $title;
    $this->slug = $slug;
    $this->body = $body;
    $this->seo_keywords = $seo_keywords;
    $this->published_at = $published_at;
    $this->user_id = $user_id;
  }

  public function validate(): bool
  {
    if (empty($this->title)) {
      throw new InvalidArgumentException('Title is required');
    }
    if (empty($this->slug)) {
      throw new InvalidArgumentException('Slug is required');
    }
    if (empty($this->user_id)) {
      throw new InvalidArgumentException('User ID is required');
    }

    return true;
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT
          blog_posts.*,
          CONCAT(users.first_name, ' ', users.last_name) as user_name
        FROM
          blog_posts
          LEFT JOIN users ON blog_posts.user_id = users.id
        WHERE
          blog_posts.deleted_at IS NULL 
        ORDER BY
          blog_posts.title ASC"
      );
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in BlogPost::findAll(): " . $e->getMessage());

      return [];
    }
  }

  public static function findFiltered(array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $conditions = ['blog_posts.deleted_at IS NULL'];
      $params = [];

      // Handle keyword search
      if (!empty($filters['keyword'])) {
        $conditions[] = "(blog_posts.title LIKE ? OR blog_posts.seo_keywords LIKE ?)";
        $searchTerm = "%" . $filters['keyword'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
      }

      // Handle published filter
      if (isset($filters['is_published'])) {
        if ($filters['is_published']) {
          $conditions[] = "blog_posts.published_at IS NOT NULL";
        } else {
          $conditions[] = "blog_posts.published_at IS NULL";
        }
      }

      // Handle user filter
      if (!empty($filters['user_id'])) {
        $conditions[] = "blog_posts.user_id = ?";
        $params[] = $filters['user_id'];
      }

      // Build the WHERE clause
      $whereClause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

      // Validate sort parameters
      $allowedSortFields = ['title', 'created_at', 'published_at', 'updated_at'];
      $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
      $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

      // Count total records
      $countSql = "SELECT COUNT(*) 
                   FROM blog_posts 
                   $whereClause";
      $stmt = $db->prepare($countSql);
      $stmt->execute($params);
      $total = (int)$stmt->fetchColumn();

      // Calculate pagination
      $page = max(1, $page);
      $perPage = max(1, min($perPage, 100));
      $offset = ($page - 1) * $perPage;
      $lastPage = max(1, ceil($total / $perPage));

      // Main query with pagination
      $sql = "SELECT 
                blog_posts.*,
                CONCAT(users.first_name, ' ', users.last_name) as user_name
              FROM 
                blog_posts
                LEFT JOIN users ON blog_posts.user_id = users.id
              $whereClause
              ORDER BY 
                blog_posts.$sortBy $sortOrder
              LIMIT ? OFFSET ?";

      $stmt = $db->prepare($sql);
      $params[] = $perPage;
      $params[] = $offset;
      $stmt->execute($params);
      $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return [
        'data' => $posts,
        'total' => $total,
        'per_page' => $perPage,
        'page' => $page,
        'last_page' => $lastPage
      ];

    } catch (PDOException $e) {
      error_log("Database error in BlogPost::findFiltered(): " . $e->getMessage());

      return [
        'data' => [],
        'total' => 0,
        'per_page' => $perPage,
        'page' => 1,
        'last_page' => 1
      ];
    }
  }

  public static function findAllPublished(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT
            blog_posts.*
            CONCAT(users.first_name, ' ', users.last_name) as user_name
          FROM
            blog_posts
            LEFT JOIN users ON blog_posts.user_id = users.id
          WHERE
            blog_posts.published_at IS NOT NULL
            AND blog_posts.deleted_at IS NULL 
          ORDER BY
            blog_posts.published_at DESC"
      );
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in BlogPost::findAllPublished(): " . $e->getMessage());

      return [];
    }
  }

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT
          blog_posts.*,
          CONCAT(users.first_name, ' ', users.last_name) as user_name
        FROM
          blog_posts
          LEFT JOIN users ON blog_posts.user_id = users.id
        WHERE
          blog_posts.id = ?
          AND blog_posts.deleted_at IS NULL"
      );
      $stmt->execute([$id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in BlogPost::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function findBySlug(string $slug): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $stmt = $db->prepare(
        "SELECT
          blog_posts.*,
          CONCAT(users.first_name, ' ', users.last_name) as user_name
        FROM
          blog_posts
          LEFT JOIN users ON blog_posts.user_id = users.id
        WHERE
          blog_posts.slug = ?
          AND blog_posts.deleted_at IS NULL
        LIMIT 1"
      );
      $stmt->execute([$slug]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row) {
        return $row;
      }

      return null;
    } catch (PDOException $e) {
      error_log("Database error in Store::findByUrl(): " . $e->getMessage());

      return null;
    }
  }

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      if (!$this->validate()) {
        return false;
      }

      if ($this->id === null) {
        $stmt = $db->prepare(
          "INSERT INTO blog_posts (
            title,
            slug,
            body,
            seo_keywords,
            user_id,
            created_at,
            updated_at,
            published_at
          ) VALUES (
            :title, 
            :slug, 
            :body, 
            :seo_keywords,
            :user_id,
            NOW(),
            NOW(),
            :published_at
          )"
        );
      } else {
        $stmt = $db->prepare(
          "UPDATE
              blog_posts
            SET
              title = :title,
              slug = :slug,
              body = :body,
              seo_keywords = :seo_keywords,
              user_id = :user_id,
              updated_at = NOW(),
              published_at = :published_at
            WHERE
              id = :id
          "
        );

        $stmt->bindValue(':id', $this->id);
      }

      $stmt->bindValue(':title', $this->title);
      $stmt->bindValue(':slug', $this->slug);
      $stmt->bindValue(':body', $this->body);
      $stmt->bindValue(':seo_keywords', $this->seo_keywords);
      $stmt->bindValue(':user_id', $this->user_id);
      $stmt->bindValue(':published_at', $this->published_at ? $this->published_at->format('Y-m-d H:i:s') : null);

      $result = $stmt->execute();
      if ($result) {
        return true;
      }

      return false;
    } catch (PDOException $e) {
      error_log("BlogPost data before save: " . print_r([
        'id' => $this->id,
        'title' => $this->title,
        'slug' => $this->slug,
        'body' => $this->body,
        'seo_keywords' => $this->seo_keywords,
        'user_id' => $this->user_id,
        'published_at' => $this->published_at
      ], true));

      error_log("Database error in BlogPost::save(): " . $e->getMessage());
      error_log("SQL State: " . $e->getCode());

      return false;
    }
  }

  public function softDelete(): bool
  {
    if ($this->id === null) {
      return false;
    }
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("UPDATE blog_posts SET deleted_at = NOW() WHERE id = ?");

    return $stmt->execute([$this->id]);
  }

  public static function countPublished(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE published_at IS NOT NULL AND deleted_at IS NULL");
      $stmt->execute();

      return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Database error in BlogPost::countActive(): " . $e->getMessage());

      return 0;
    }
  }

  public static function countDrafts(): int
  {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM blog_posts WHERE published_at IS NULL AND deleted_at IS NULL");
    $stmt->execute();

    return (int)$stmt->fetchColumn();
  }
  ///////////////////////////////////////////////////////////////////////////////
  // Getters and Setters

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function getLogoUrl(): ?string
  {
    return $this->logo_url;
  }

  public function setLogoUrl(?string $logo_url): void
  {
    $this->logo_url = $logo_url;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(?string $url): void
  {
    $this->url = $url;
  }

  public function isActive(): bool
  {
    return $this->is_active;
  }

  public function setIsActive(bool $is_active): void
  {
    $this->is_active = $is_active;
  }

  public function getCreatedAt(): ?string
  {
    return $this->created_at;
  }

  public function getUpdatedAt(): ?string
  {
    return $this->updated_at;
  }

  public function getDeletedAt(): ?string
  {
    return $this->deleted_at;
  }

  public function initFromArray(array $data): void
  {
    $this->id = isset($data['id']) ? (int)$data['id'] : null;
    $this->title = $data['title'] ?? '';
    $this->slug = $data['slug'] ?? '';
    $this->body = $data['body'] ?? '';
    $this->seo_keywords = $data['seo_keywords'] ?? null;
    $this->user_id = $data['user_id'] ?? 0;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'slug' => $this->slug,
      'body' => $this->body,
      'seo_keywords' => $this->seo_keywords,
      'user_id' => $this->user_id,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'deleted_at' => $this->deleted_at,
      'published_at' => $this->published_at
    ];
  }
}
