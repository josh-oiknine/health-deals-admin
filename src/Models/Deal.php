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
  private string $title;
  private string $description;
  private string $affiliate_url;
  private string $image_url;
  private int $product_id;
  private int $store_id;
  private ?int $category_id;
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
    // Validate required fields
    if (empty($title)) {
      throw new InvalidArgumentException('Title is required');
    }
    if (empty($description)) {
      throw new InvalidArgumentException('Description is required');
    }
    if (empty($affiliate_url)) {
      throw new InvalidArgumentException('Affiliate URL is required');
    }
    if ($store_id <= 0) {
      throw new InvalidArgumentException('Valid store ID is required');
    }
    if ($product_id <= 0) {
      throw new InvalidArgumentException('Valid product ID is required');
    }
    if ($original_price < 0) {
      throw new InvalidArgumentException('Original price must be non-negative');
    }
    if ($deal_price < 0) {
      throw new InvalidArgumentException('Deal price must be non-negative');
    }

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

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      if ($this->id === null) {
        // Insert new deal
        $sql = "INSERT INTO deals (
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
                )";
      } else {
        // Update existing deal
        $sql = "UPDATE deals SET
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
                    id = :id";
      }

      $stmt = $db->prepare($sql);

      $params = [
        'store_id' => $this->store_id,
        'product_id' => $this->product_id,
        'category_id' => $this->category_id,
        'title' => $this->title,
        'description' => $this->description,
        'deal_price' => $this->deal_price,
        'original_price' => $this->original_price,
        'image_url' => $this->image_url,
        'affiliate_url' => $this->affiliate_url,
        'is_active' => $this->is_active ? 't' : 'f',  // Convert boolean to PostgreSQL format
        'is_featured' => $this->is_featured ? 't' : 'f',  // Convert boolean to PostgreSQL format
        'is_expired' => $this->is_expired ? 't' : 'f'  // Convert boolean to PostgreSQL format
      ];

      if ($this->id !== null) {
        $params['id'] = $this->id;
      }

      return $stmt->execute($params);
    } catch (PDOException $e) {
      error_log("Error saving deal: " . $e->getMessage());
      throw $e; // Re-throw the exception to handle it in the controller
    }
  }

  public static function findFiltered(array $filters, string $sortBy = 'created_at', string $sortOrder = 'DESC', int $page = 1, int $perPage = 20): array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $conditions = ['1=1'];
      $params = [];

      // Apply filters
      if (!empty($filters['keyword'])) {
        $conditions[] = "(d.title LIKE :keyword OR d.description LIKE :keyword)";
        $params['keyword'] = "%{$filters['keyword']}%";
      }

      if (!empty($filters['store_id'])) {
        $conditions[] = "d.store_id = :store_id";
        $params['store_id'] = $filters['store_id'];
      }

      if (isset($filters['category_id'])) {
        if ($filters['category_id'] === 0) {
          $conditions[] = "d.category_id IS NULL";
        } else {
          $conditions[] = "d.category_id = :category_id";
          $params['category_id'] = $filters['category_id'];
        }
      }

      if (isset($filters['is_active'])) {
        $conditions[] = "d.is_active = :is_active";
        $params['is_active'] = $filters['is_active'];
      }

      // Build the query
      $sql = "SELECT 
                d.*,
                s.name as store_name,
                c.name as category_name,
                c.color as category_color,
                p.name as product_name
            FROM deals d
            LEFT JOIN stores s ON d.store_id = s.id
            LEFT JOIN categories c ON d.category_id = c.id
            LEFT JOIN products p ON d.product_id = p.id
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
      $countSql = "SELECT COUNT(*) FROM deals d WHERE " . implode(' AND ', $conditions);
      $countStmt = $db->prepare($countSql);
      $countStmt->execute($params);
      $total = $countStmt->fetchColumn();

      // Add pagination
      $offset = ($page - 1) * $perPage;
      $sql .= " LIMIT :limit OFFSET :offset";
      $params['limit'] = $perPage;
      $params['offset'] = $offset;

      // Execute the main query
      $stmt = $db->prepare($sql);
      $stmt->execute($params);
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

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();

      $sql = "SELECT 
                d.*,
                s.name as store_name,
                c.name as category_name,
                p.name as product_name
            FROM deals d
            LEFT JOIN stores s ON d.store_id = s.id
            LEFT JOIN categories c ON d.category_id = c.id
            LEFT JOIN products p ON d.product_id = p.id
            WHERE d.id = :id";

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
        SELECT * FROM deals
        ORDER BY created_at DESC
        LIMIT :limit
      ");
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Error in Deal::getLatestDeals(): " . $e->getMessage());

      return [];
    }
  }

  //   public static function delete(int $id): bool
  //   {
  //     try {
  //       $db = Database::getInstance()->getConnection();
  //       $stmt = $db->prepare("DELETE FROM deals WHERE id = :id");

  //       return $stmt->execute(['id' => $id]);
  //     } catch (PDOException $e) {
  //       error_log("Error deleting deal: " . $e->getMessage());

  //       return false;
  //     }
  //   }
}
