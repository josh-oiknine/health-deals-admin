<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

class Deal
{
  private ?int $id = null;
  private int $product_id;
  private int $store_id;
  private ?int $category_id;
  private string $title;
  private string $description;
  private string $affiliate_url;
  private string $image_url;
  private float $original_price;
  private float $deal_price;
  private bool $is_active;
  private bool $is_featured;
  private bool $is_expired;
  private ?DateTime $created_at = null;
  private ?DateTime $updated_at = null;

  public function __construct(
    string $title,
    string $description,
    string $affiliate_url,
    string $image_url,
    int $product_id,
    int $store_id,
    ?int $category_id,
    float $original_price,
    float $deal_price,
    bool $is_active = true,
    bool $is_featured = false,
    bool $is_expired = false
  ) {
    $this->title = $title;
    $this->description = $description;
    $this->affiliate_url = $affiliate_url;
    $this->image_url = $image_url;
    $this->product_id = $product_id;
    $this->store_id = $store_id;
    $this->category_id = $category_id;
    $this->original_price = $original_price;
    $this->deal_price = $deal_price;
    $this->is_active = $is_active;
    $this->is_featured = $is_featured;
    $this->is_expired = $is_expired;
  }

  public function validate(): bool
  {
    // Validate required fields
    if (empty($this->title)) {
      throw new InvalidArgumentException('Title is required');
    }
    if (empty($this->description)) {
      throw new InvalidArgumentException('Description is required');
    }
    if (empty($this->affiliate_url)) {
      throw new InvalidArgumentException('Affiliate URL is required');
    }
    if ($this->store_id <= 0) {
      throw new InvalidArgumentException('Valid store ID is required');
    }
    if ($this->product_id <= 0) {
      throw new InvalidArgumentException('Valid product ID is required');
    }
    if ($this->original_price < 0) {
      throw new InvalidArgumentException('Original price must be non-negative');
    }
    if ($this->deal_price < 0) {
      throw new InvalidArgumentException('Deal price must be non-negative');
    }

    return true;
  }

  public static function findFiltered(array $filters, string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $conditions = ['1=1'];
      $params = [];

      // Apply filters
      if (!empty($filters['keyword'])) {
        $conditions[] = "(deals.title LIKE :keyword OR deals.description LIKE :keyword OR products.sku LIKE :keyword OR products.upc LIKE :keyword)";
        $params['keyword'] = "%{$filters['keyword']}%";
      }

      if (!empty($filters['store_id'])) {
        $conditions[] = "deals.store_id = :store_id";
        $params['store_id'] = $filters['store_id'];
      }

      if (isset($filters['category_id'])) {
        if ($filters['category_id'] === 0) {
          $conditions[] = "deals.category_id IS NULL";
        } else {
          $conditions[] = "deals.category_id = :category_id";
          $params['category_id'] = $filters['category_id'];
        }
      }

      if (isset($filters['is_active'])) {
        $conditions[] = "deals.is_active = :is_active";
        $params['is_active'] = $filters['is_active'];
      }

      // Build the query
      $sql = "SELECT 
                deals.*,
                stores.name as store_name,
                categories.name as category_name,
                categories.color as category_color,
                products.name as product_name,
                products.sku as product_sku,
                products.upc as product_upc
            FROM deals
                LEFT JOIN stores ON deals.store_id = stores.id
                LEFT JOIN categories ON deals.category_id = categories.id
                LEFT JOIN products ON deals.product_id = products.id
            WHERE " . implode(' AND ', $conditions);

      // Add sorting
      $allowedSortFields = [
        'title', 'store_name', 'category_name', 'original_price',
        'deal_price', 'created_at', 'updated_at'
      ];

      $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
      $sortOrder = $sortOrder === 'ASC' ? 'ASC' : 'DESC';

      $sql .= " ORDER BY $sortBy $sortOrder";

      // Get total count for pagination
      $countSql = "SELECT COUNT(*) FROM deals LEFT JOIN products ON deals.product_id = products.id WHERE " . implode(' AND ', $conditions);
      $countStmt = $db->prepare($countSql);
      // Bind boolean parameters correctly
      foreach ($params as $key => $value) {
        if ($key === 'is_active') {
          $countStmt->bindValue(":$key", $value, PDO::PARAM_BOOL);
        } else {
          $countStmt->bindValue(":$key", $value);
        }
      }
      $countStmt->execute();
      $total = $countStmt->fetchColumn();

      // Add pagination
      $offset = ($page - 1) * $perPage;
      $sql .= " LIMIT :limit OFFSET :offset";
      $params['limit'] = $perPage;
      $params['offset'] = $offset;

      // Execute the main query
      $stmt = $db->prepare($sql);
      
      // Bind boolean parameters correctly
      foreach ($params as $key => $value) {
        if ($key === 'is_active') {
          $stmt->bindValue(":$key", $value, PDO::PARAM_BOOL);
        } else {
          $stmt->bindValue(":$key", $value);
        }
      }

      $stmt->execute();
      $deals = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return [
        'data' => $deals,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => ceil($total / $perPage)
      ];
    } catch (PDOException $e) {
      error_log("Error in Deal::findFiltered(): " . $e->getMessage());

      return [
        'data' => [],
        'total' => 0,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => 1
      ];
    }
  }

  public static function findById(int $id, bool $active = true): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $sql = "SELECT 
          d.*,
          s.name as store_name,
          c.name as category_name,
          p.name as product_name
        FROM
          deals d
          LEFT JOIN stores s ON d.store_id = s.id
          LEFT JOIN categories c ON d.category_id = c.id
          LEFT JOIN products p ON d.product_id = p.id
        WHERE
          d.id = :id";

      if ($active) {
        $sql .= " AND (d.is_active = TRUE OR d.is_expired = FALSE)";
      }

      $stmt = $db->prepare($sql);
      $stmt->execute(['id' => $id]);

      $deal = $stmt->fetch(PDO::FETCH_ASSOC);

      return $deal ?: null;
    } catch (PDOException $e) {
      error_log("Error in Deal::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function countActive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT COUNT(*) FROM deals
        WHERE is_active = TRUE
      ");
      $stmt->execute();

      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Error in Deal::countActive(): " . $e->getMessage());

      return 0;
    }
  }

  public static function countInactive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT COUNT(*) FROM deals
        WHERE is_active = FALSE
      ");
      $stmt->execute();

      return $stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Error in Deal::countInactive(): " . $e->getMessage());

      return 0;
    }
  }

  public static function getDealsPerDay(int $days = 7): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT DATE(created_at) as date, COUNT(*) as count
        FROM deals
        WHERE created_at >= NOW() - (INTERVAL '1 day' * :days)
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at)
      ");
      $stmt->bindParam(':days', $days, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
      error_log("Error in Deal::getDealsPerDay(): " . $e->getMessage());

      return [];
    }
  }

  public static function getLatestDeals(int $limit = 18): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT
            deals.*,
            products.sku as product_sku
        FROM
            deals
            LEFT JOIN products on deals.product_id = products.id
        ORDER BY
            deals.created_at DESC
        LIMIT
            :limit
      ");
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error in Deal::getLatestDeals(): " . $e->getMessage());

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
        // Insert new deal
        $stmt = $db->prepare(
          "INSERT INTO deals (
                    store_id,
                    product_id,
                    category_id,
                    title,
                    description,
                    deal_price,
                    original_price,
                    image_url,
                    affiliate_url,
                    is_active,
                    is_featured,
                    is_expired,
                    created_at,
                    updated_at
                ) VALUES (
                    :store_id,
                    :product_id,
                    :category_id,
                    :title,
                    :description,
                    :deal_price,
                    :original_price,
                    :image_url,
                    :affiliate_url,
                    :is_active,
                    :is_featured,
                    :is_expired,
                    NOW(),
                    NOW()
                )"
        );
      } else {
        // Update existing deal
        $stmt = $db->prepare(
          "UPDATE deals SET
                    store_id = :store_id,
                    product_id = :product_id,
                    category_id = :category_id,
                    title = :title,
                    description = :description,
                    deal_price = :deal_price,
                    original_price = :original_price,
                    image_url = :image_url,
                    affiliate_url = :affiliate_url,
                    is_active = :is_active,
                    is_featured = :is_featured,
                    is_expired = :is_expired,
                    updated_at = NOW()
                WHERE
                    id = :id"
        );

        $stmt->bindValue(':id', $this->id);
      }

      $stmt->bindValue(':store_id', $this->store_id);
      $stmt->bindValue(':product_id', $this->product_id);
      $stmt->bindValue(':category_id', $this->category_id);
      $stmt->bindValue(':title', $this->title);
      $stmt->bindValue(':description', $this->description);
      $stmt->bindValue(':deal_price', $this->deal_price);
      $stmt->bindValue(':original_price', $this->original_price);
      $stmt->bindValue(':image_url', $this->image_url);
      $stmt->bindValue(':affiliate_url', $this->affiliate_url);
      $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_BOOL);
      $stmt->bindValue(':is_featured', $this->is_featured, PDO::PARAM_BOOL);
      $stmt->bindValue(':is_expired', $this->is_expired, PDO::PARAM_BOOL);

      $result = $stmt->execute();
      if ($result) {
        return true;
      }

      return false;
    } catch (PDOException $e) {
      error_log("Error saving deal: " . $e->getMessage());
      throw $e; // Re-throw the exception to handle it in the controller
    }
  }

  public function delete(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      // Start transaction
      $db->beginTransaction();

      // Get the product_id before deleting the deal
      $stmt = $db->prepare("SELECT product_id FROM deals WHERE id = :id");
      $stmt->execute(['id' => $id]);
      $deal = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($deal) {
        // Delete price history records for the product
        $stmt = $db->prepare("DELETE FROM price_history WHERE product_id = :product_id");
        $stmt->execute(['product_id' => $deal['product_id']]);

        // Update the product's last_checked to NULL
        $stmt = $db->prepare("UPDATE products SET last_checked = NULL WHERE id = :product_id");
        $stmt->execute(['product_id' => $deal['product_id']]);

        // Delete the deal
        $stmt = $db->prepare("DELETE FROM deals WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Commit transaction
        $db->commit();

        return true;
      }

      $db->rollBack();

      return false;
    } catch (PDOException $e) {
      error_log("Error deleting deal: " . $e->getMessage());
      if ($db->inTransaction()) {
        $db->rollBack();
      }

      return false;
    }
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): void
  {
    $this->title = $title;
  }

  public function getDescription(): string
  {
    return $this->description;
  }

  public function setDescription(string $description): void
  {
    $this->description = $description;
  }

  public function getAffiliateUrl(): string
  {
    return $this->affiliate_url;
  }

  public function setAffiliateUrl(string $affiliate_url): void
  {
    $this->affiliate_url = $affiliate_url;
  }

  public function getImageUrl(): string
  {
    return $this->image_url;
  }

  public function setImageUrl(string $image_url): void
  {
    $this->image_url = $image_url;
  }

  public function getProductId(): int
  {
    return $this->product_id;
  }

  public function setProductId(int $product_id): void
  {
    $this->product_id = $product_id;
  }

  public function getStoreId(): int
  {
    return $this->store_id;
  }

  public function setStoreId(int $store_id): void
  {
    $this->store_id = $store_id;
  }

  public function getCategoryId(): ?int
  {
    return $this->category_id;
  }

  public function setCategoryId(?int $category_id): void
  {
    $this->category_id = $category_id;
  }

  public function getOriginalPrice(): float
  {
    return $this->original_price;
  }

  public function setOriginalPrice(float $original_price): void
  {
    $this->original_price = $original_price;
  }

  public function getDealPrice(): float
  {
    return $this->deal_price;
  }

  public function setDealPrice(float $deal_price): void
  {
    $this->deal_price = $deal_price;
  }

  public function isActive(): bool
  {
    return $this->is_active;
  }

  public function setIsActive(bool $is_active): void
  {
    $this->is_active = $is_active;
  }

  public function isFeatured(): bool
  {
    return $this->is_featured;
  }

  public function setIsFeatured(bool $is_featured): void
  {
    $this->is_featured = $is_featured;
  }

  public function isExpired(): bool
  {
    return $this->is_expired;
  }

  public function setIsExpired(bool $is_expired): void
  {
    $this->is_expired = $is_expired;
  }

  public function getCreatedAt(): ?DateTime
  {
    return $this->created_at;
  }

  public function getUpdatedAt(): ?DateTime
  {
    return $this->updated_at;
  }

  public function initFromArray(array $data): void
  {
    $this->id = isset($data['id']) ? (int)$data['id'] : null;
    $this->title = $data['title'] ?? '';
    $this->description = $data['description'] ?? '';
    $this->affiliate_url = $data['affiliate_url'] ?? '';
    $this->image_url = $data['image_url'] ?? '';
    $this->product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
    $this->store_id = isset($data['store_id']) ? (int)$data['store_id'] : 0;
    $this->category_id = isset($data['category_id']) ? (int)$data['category_id'] : null;
    $this->original_price = isset($data['original_price']) ? (float)$data['original_price'] : 0.0;
    $this->deal_price = isset($data['deal_price']) ? (float)$data['deal_price'] : 0.0;
    $this->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $this->is_featured = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;
    $this->is_expired = isset($data['is_expired']) ? (bool)$data['is_expired'] : false;
    $this->created_at = isset($data['created_at']) ? new DateTime($data['created_at']) : null;
    $this->updated_at = isset($data['updated_at']) ? new DateTime($data['updated_at']) : null;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'product_id' => $this->product_id,
      'store_id' => $this->store_id,
      'category_id' => $this->category_id,
      'title' => $this->title,
      'description' => $this->description,
      'affiliate_url' => $this->affiliate_url,
      'image_url' => $this->image_url,
      'original_price' => $this->original_price,
      'deal_price' => $this->deal_price,
      'is_active' => $this->is_active,
      'is_featured' => $this->is_featured,
      'is_expired' => $this->is_expired,
      'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
      'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null
    ];
  }
}
