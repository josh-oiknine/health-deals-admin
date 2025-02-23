<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

class Product
{
  private ?int $id = null;
  private int $store_id = 0;
  private string $name;
  private string $slug;
  private string $url;
  private ?int $category_id = null;
  private float $regular_price = 0.0;
  private ?string $sku;
  private ?string $upc = null;
  private bool $is_active = true;
  private ?int $user_id = null;
  private ?DateTime $created_at = null;
  private ?DateTime $updated_at = null;
  private ?DateTime $deleted_at = null;
  private ?DateTime $last_checked = null;

  public function __construct(
    string $name = '',
    string $slug = '',
    string $url = '',
    ?int $category_id = null,
    int $store_id = 0,
    float $regular_price = 0.0,
    ?string $sku = null,
    ?string $upc = null,
    bool $is_active = true,
    ?int $user_id = null
  ) {
    $this->name = $name;
    $this->slug = $slug;
    $this->url = $url;
    $this->category_id = $category_id;
    $this->store_id = $store_id;
    $this->regular_price = $regular_price;
    $this->sku = $sku;
    $this->upc = $upc;
    $this->is_active = $is_active;
    $this->user_id = $user_id;
  }

  public function validate(): bool
  {
    if (empty($this->name)) {
      throw new InvalidArgumentException('Name is required');
    }
    if (empty($this->slug)) {
      throw new InvalidArgumentException('Slug is required');
    }
    if (empty($this->url)) {
      throw new InvalidArgumentException('URL is required');
    }
    if (empty($this->sku)) {
      throw new InvalidArgumentException('SKU is required');
    }
    if ($this->store_id <= 0) {
      throw new InvalidArgumentException('Store ID is required');
    }
    if ($this->regular_price < 0) {
      throw new InvalidArgumentException('Regular price must be non-negative');
    }

    return true;
  }

  public static function findFiltered(array $filters = [], string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array
  {
    try {
      $db = Database::getInstance()->getConnection();

      // Base query
      $query = "
        SELECT
          {fields}
        FROM
          products
          LEFT JOIN stores ON products.store_id = stores.id
          LEFT JOIN categories ON products.category_id = categories.id
          LEFT JOIN users ON products.user_id = users.id
        WHERE
          products.deleted_at IS NULL
      ";

      $params = [];

      // Apply filters
      if (!empty($filters['keyword'])) {
        $keyword = '%' . $filters['keyword'] . '%';
        $query .= " AND (products.name LIKE :keyword OR products.sku LIKE :keyword OR CAST(products.regular_price AS CHAR) LIKE :keyword)";
        $params['keyword'] = $keyword;
      }

      if (!empty($filters['store_id'])) {
        $query .= " AND products.store_id = :store_id";
        $params['store_id'] = $filters['store_id'];
      }

      if (isset($filters['category_id'])) {
        if ($filters['category_id'] === 0) {
          $query .= " AND products.category_id IS NULL";
        } else {
          $query .= " AND products.category_id = :category_id";
          $params['category_id'] = $filters['category_id'];
        }
      }

      if (isset($filters['is_active'])) {
        $query .= " AND products.is_active = :is_active";
        $params['is_active'] = $filters['is_active'];
      }

      if (isset($filters['user_id'])) {
        if ($filters['user_id'] === 0) {
          $query .= " AND products.user_id IS NULL";
        } else {
          $query .= " AND products.user_id = :user_id";
          $params['user_id'] = $filters['user_id'];
        }
      }

      // Count total results
      $countQuery = str_replace("{fields}", "COUNT(*)", $query);
      $countStmt = $db->prepare($countQuery);
      $countStmt->execute($params);
      $total = (int)$countStmt->fetchColumn();

      // Apply sorting
      $allowedSortFields = [
        'name' => 'products.name',
        'store_name' => 'stores.name',
        'category_name' => 'categories.name',
        'sku' => 'products.sku',
        'regular_price' => 'products.regular_price',
        'created_at' => 'products.created_at',
        'updated_at' => 'products.updated_at',
        'last_checked' => 'products.last_checked'
      ];

      $sortField = $allowedSortFields[$sortBy] ?? 'products.created_at';
      $query .= " ORDER BY {$sortField} {$sortOrder}";

      // Apply pagination
      $offset = ($page - 1) * $perPage;
      $query .= " LIMIT :limit OFFSET :offset";

      $query = str_replace("{fields}", "products.*, stores.name as store_name, stores.logo_url as store_logo_url, categories.name as category_name, categories.color as category_color, CONCAT(users.first_name, ' ', users.last_name) as user_name", $query);
      $stmt = $db->prepare($query);

      // Bind all parameters
      foreach ($params as $key => $value) {
        $stmt->bindValue(":{$key}", $value);
      }
      $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

      $stmt->execute();
      $data = $stmt->fetchAll();

      $lastPage = ceil($total / $perPage);

      return [
        'data' => $data,
        'total' => $total,
        'per_page' => $perPage,
        'page' => $page,
        'last_page' => $lastPage
      ];
    } catch (PDOException $e) {
      error_log("Database error in Product::findFiltered(): " . $e->getMessage());

      return [
        'data' => [],
        'total' => 0,
        'per_page' => $perPage,
        'page' => $page,
        'last_page' => 1
      ];
    }
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT
          products.*,
          stores.name as store_name,
          stores.logo_url as store_logo_url,
          categories.name as category_name,
          categories.color as category_color,
          CONCAT(users.first_name, ' ', users.last_name) as user_name
        FROM
          products
          LEFT JOIN stores ON products.store_id = stores.id 
          LEFT JOIN categories ON products.category_id = categories.id
          LEFT JOIN users ON products.user_id = users.id
        WHERE
          products.deleted_at IS NULL
        ORDER BY
          products.created_at DESC
      ");
      $stmt->execute();

      return $stmt->fetchAll();
    } catch (PDOException $e) {
      error_log("Database error in Product::findAll(): " . $e->getMessage());

      return [];
    }
  }

  public static function findByStore(int $store_id): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT p.*, s.name as store_name, c.name as category_name 
         FROM products p 
         LEFT JOIN stores s ON p.store_id = s.id 
         LEFT JOIN categories c ON p.category_id = c.id 
         WHERE p.store_id = ? 
         AND p.deleted_at IS NULL 
         ORDER BY p.name ASC"
      );
      $stmt->execute([$store_id]);

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in Product::findByStore(): " . $e->getMessage());

      return [];
    }
  }

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT p.*, s.name as store_name, c.name as category_name 
         FROM products p 
         LEFT JOIN stores s ON p.store_id = s.id 
         LEFT JOIN categories c ON p.category_id = c.id 
         WHERE p.id = ? 
         AND p.deleted_at IS NULL"
      );
      $stmt->execute([$id]);

      return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
      error_log("Database error in Product::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function findBySku(string $sku): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT p.*, s.name as store_name, c.name as category_name 
         FROM products p 
         LEFT JOIN stores s ON p.store_id = s.id 
         LEFT JOIN categories c ON p.category_id = c.id 
         WHERE p.sku = ? 
         AND p.deleted_at IS NULL"
      );
      $stmt->execute([$sku]);

      return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
      error_log("Database error in Product::findBySku(): " . $e->getMessage());

      return null;
    }
  }

  public static function findBySlug(string $slug): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT * FROM products WHERE slug = :slug AND deleted_at IS NULL
      ");
      $stmt->execute(['slug' => $slug]);
      $result = $stmt->fetch();

      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in Product::findBySlug(): " . $e->getMessage());

      return null;
    }
  }

  public static function countBySlug(string $slug): ?int
  {
    return self::findBySlug($slug) ? 1 : 0;
  }

  public static function countActive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE is_active = true AND deleted_at IS NULL");
      $stmt->execute();

      return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Database error in Product::countActive(): " . $e->getMessage());

      return 0;
    }
  }

  public static function countInactive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT COUNT(*) FROM products 
        WHERE is_active = FALSE
      ");
      $stmt->execute();

      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Error in Product::countInactive(): " . $e->getMessage());

      return 0;
    }
  }

  public static function getProductsPerDay(int $days = 7): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM products
        WHERE created_at >= NOW() - (INTERVAL '1 day' * :days)
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at)
      ");
      $stmt->bindParam(':days', $days, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
      error_log("Error in Product::getProductsPerDay(): " . $e->getMessage());

      return [];
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
          "INSERT INTO products (
            name,
            slug,
            url,
            category_id,
            store_id,
            regular_price,
            sku,
            upc,
            is_active,
            user_id,
            created_at,
            updated_at
          ) VALUES (
            :name,
            :slug,
            :url,
            :category_id,
            :store_id,
            :regular_price,
            :sku,
            :upc,
            :is_active,
            :user_id,
            NOW(),
            NOW()
          )
        "
        );
      } else {
        $stmt = $db->prepare(
          "UPDATE products 
            SET
              name = :name, 
              slug = :slug,
              url = :url,
              category_id = :category_id,
              store_id = :store_id,
              regular_price = :regular_price,
              sku = :sku,
              upc = :upc,
              is_active = :is_active,
              user_id = :user_id,
              updated_at = NOW()
            WHERE
              id = :id
          "
        );
        $stmt->bindValue(':id', $this->id);
      }

      $stmt->bindValue(':name', $this->name);
      $stmt->bindValue(':slug', $this->slug);
      $stmt->bindValue(':url', $this->url);
      $stmt->bindValue(':category_id', $this->category_id);
      $stmt->bindValue(':store_id', $this->store_id);
      $stmt->bindValue(':regular_price', $this->regular_price);
      $stmt->bindValue(':sku', $this->sku);
      $stmt->bindValue(':upc', $this->upc);
      $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_BOOL);
      $stmt->bindValue(':user_id', $this->user_id);

      $result = $stmt->execute();

      if (!$result) {
        $error = $stmt->errorInfo();
        error_log("Database error in Product::save(): " . json_encode($error));
      }

      return $result;
    } catch (PDOException $e) {
      error_log("Database error in Product::save(): " . $e->getMessage());

      return false;
    }
  }

  public function softDelete(): bool
  {
    if ($this->id === null) {
      return false;
    }

    try {
      $db = Database::getInstance()->getConnection();

      // Look for deals with this product. If there are, do not delete.
      $stmt = $db->prepare("SELECT COUNT(*) FROM deals WHERE product_id = ? AND deleted_at IS NULL");
      $stmt->execute([$this->id]);
      $result = $stmt->fetchColumn();
      if ($result > 0) {
        return false;
      }

      $stmt = $db->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");

      return $stmt->execute([$this->id]);
    } catch (PDOException $e) {
      error_log("Database error in Product::softDelete(): " . $e->getMessage());

      return false;
    }
  }

  ///////////////////////////////////////////////////////////////////////////////
  // Getters and setters
  ///////////////////////////////////////////////////////////////////////////////
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(?int $id): void
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

  public function getSlug(): string
  {
    return $this->slug;
  }
  public function setSlug(string $slug): void
  {
    $this->slug = $slug;
  }

  public function getUrl(): string
  {
    return $this->url;
  }
  public function setUrl(string $url): void
  {
    $this->url = $url;
  }

  public function getCategoryId(): ?int
  {
    return $this->category_id;
  }
  public function setCategoryId(?int $category_id): void
  {
    $this->category_id = $category_id;
  }

  public function getStoreId(): int
  {
    return $this->store_id;
  }
  public function setStoreId(int $store_id): void
  {
    $this->store_id = $store_id;
  }

  public function getRegularPrice(): float
  {
    return $this->regular_price;
  }
  public function setRegularPrice(float $regular_price): void
  {
    $this->regular_price = $regular_price;
  }

  public function getSku(): ?string
  {
    return $this->sku;
  }
  public function setSku(?string $sku): void
  {
    $this->sku = $sku;
  }

  public function getUpc(): ?string
  {
    return $this->upc;
  }

  public function setUpc(?string $upc): void
  {
    $this->upc = $upc;
  }

  public function getIsActive(): bool
  {
    return $this->is_active;
  }
  public function setIsActive(bool $is_active): void
  {
    $this->is_active = $is_active;
  }

  public function getUserId(): ?int
  {
    return $this->user_id;
  }

  public function setUserId(?int $user_id): void
  {
    $this->user_id = $user_id;
  }

  public function getLastChecked(): ?string
  {
    return $this->last_checked;
  }
  public function setLastChecked(?string $last_checked): void
  {
    $this->last_checked = $last_checked;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'store_id' => $this->store_id,
      'name' => $this->name,
      'slug' => $this->slug,
      'url' => $this->url,
      'category_id' => $this->category_id,
      'regular_price' => $this->regular_price,
      'sku' => $this->sku,
      'upc' => $this->upc,
      'is_active' => $this->is_active,
      'user_id' => $this->user_id,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'deleted_at' => $this->deleted_at,
      'last_checked' => $this->last_checked,
      'store' => $this->store ? $this->store->toArray() : null
    ];
  }

  ///////////////////////////////////////////////////////////////////////////////
}
