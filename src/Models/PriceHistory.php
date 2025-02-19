<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDOException;

class PriceHistory
{
  private ?int $id = null;
  private int $product_id;
  private float $price;
  private ?string $created_at = null;

  public function __construct(
    int $product_id = 0,
    float $price = 0.0,
    ?string $created_at = null
  ) {
    $this->product_id = $product_id;
    $this->price = $price;
    $this->created_at = $created_at;
  }

  public static function findByProduct(int $product_id, int $limit = 99999999): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
                SELECT * FROM price_history 
                WHERE product_id = :product_id
                ORDER BY created_at DESC
                LIMIT :limit
            ");
      $stmt->execute(['product_id' => $product_id, 'limit' => $limit]);

      return $stmt->fetchAll();
    } catch (PDOException $e) {
      error_log("Database error in PriceHistory::findByProduct(): " . $e->getMessage());

      return [];
    }
  }

  public static function countByProduct(int $product_id): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT COUNT(*) FROM price_history WHERE product_id = :product_id");
      $stmt->execute(['product_id' => $product_id]);

      return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Database error in PriceHistory::countByProduct(): " . $e->getMessage());

      return 0;
    }
  }

  // Getters and setters
  public function getId(): ?int
  {
    return $this->id;
  }
  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  public function getProductId(): int
  {
    return $this->product_id;
  }
  public function setProductId(int $product_id): void
  {
    $this->product_id = $product_id;
  }

  ////////////////////////////////////////////////////////////////////////////////
}
