<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

class Category
{
  private $id = null;
  private $name;
  private $slug;
  private $is_active;
  private $color;
  private $created_at;
  private $updated_at;

  public function __construct(
    string $name = '',
    string $slug = '',
    bool $is_active = true,
    ?string $color = '#6c757d'
  ) {
    $this->name = $name;
    $this->slug = $slug;
    $this->is_active = $is_active;
    $this->color = $color;
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT * FROM categories ORDER BY name");
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $categories = [];
      foreach ($rows as $row) {
        $category = new self();
        $category->initFromArray($row);
        $categories[] = $category;
      }

      return $categories;
    } catch (PDOException $e) {
      error_log("Database error in Category::findAll(): " . $e->getMessage());

      return [];
    }
  }

  public static function findAllActive(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
                SELECT * FROM categories 
                WHERE is_active = true 
                ORDER BY name
            ");
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in Category::findAllActive(): " . $e->getMessage());

      return [];
    }
  }

  public static function findById(int $id): ?self
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
      $stmt->execute([$id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row) {
        $category = new self();
        $category->initFromArray($row);

        return $category;
      }

      return null;
    } catch (PDOException $e) {
      error_log("Database error in Category::findById(): " . $e->getMessage());

      return null;
    }
  }

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      if ($this->id === null) {
        $stmt = $db->prepare(
          "INSERT INTO categories (name, slug, is_active, color) 
                     VALUES (?, ?, ?, ?)"
        );
        $result = $stmt->execute([$this->name, $this->slug, (int)$this->is_active, $this->color]);
        if ($result) {
          $this->id = (int)$db->lastInsertId();

          return true;
        }

        return false;
      } else {
        $stmt = $db->prepare(
          "UPDATE categories 
                     SET name = ?, slug = ?, is_active = ?, color = ?, updated_at = CURRENT_TIMESTAMP 
                     WHERE id = ?"
        );

        return $stmt->execute([$this->name, $this->slug, (int)$this->is_active, $this->color, $this->id]);
      }
    } catch (PDOException $e) {
      error_log("Database error in Category::save(): " . $e->getMessage());

      return false;
    }
  }

  public static function countActive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT COUNT(*) FROM categories WHERE is_active = true");
      $stmt->execute();

      return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Database error in Category::countActive(): " . $e->getMessage());

      return 0;
    }
  }

  // Getters and setters
  public function getId(): ?int
  {
    return $this->id;
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

  public function getColor(): ?string
  {
    return $this->color;
  }

  public function setColor(?string $color): void
  {
    $this->color = $color;
  }

  private function initFromArray(array $data): void
  {
    $this->id = isset($data['id']) ? (int)$data['id'] : null;
    $this->name = $data['name'] ?? '';
    $this->slug = $data['slug'] ?? '';
    $this->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $this->color = $data['color'] ?? '#6c757d';
    $this->created_at = $data['created_at'] ?? null;
    $this->updated_at = $data['updated_at'] ?? null;
  }
}
