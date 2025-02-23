<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use DateTime;
use InvalidArgumentException;
use PDO;
use PDOException;

class Category
{
  private $id = null;
  private $name;
  private $slug;
  private $is_active;
  private $color;
  private ?DateTime $created_at = null;
  private ?DateTime $updated_at = null;

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

  public function validate(): bool
  {
    if (empty($this->name)) {
      throw new InvalidArgumentException('Name is required');
    }
    if (empty($this->slug)) {
      throw new InvalidArgumentException('Slug is required');
    }
    if (empty($this->color)) {
      throw new InvalidArgumentException('Color is required');
    }

    return true;
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM categories
        WHERE deleted_at IS NULL
        ORDER BY name ASC"
      );
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in Category::findAll(): " . $e->getMessage());
      return [];
    }
  }

  public static function findAllActive(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM categories 
        WHERE
          is_active = true
          AND deleted_at IS NULL
        ORDER BY name ASC"
      );
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in Category::findAllActive(): " . $e->getMessage());
      return [];
    }
  }

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM categories 
         WHERE id = ?"
      );
      $stmt->execute([$id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in Category::findById(): " . $e->getMessage());
      return null;
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

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      if (!$this->validate()) {
        return false;
      }

      if ($this->id === null) {
        $stmt = $db->prepare(
          "INSERT INTO categories (
            name, 
            slug, 
            is_active, 
            color,
            created_at,
            updated_at
          ) VALUES (
            :name,
            :slug,
            :is_active,
            :color,
            NOW(),
            NOW()
          )"
        );
      } else {
        $stmt = $db->prepare(
          "UPDATE categories 
            SET
              name = :name,
              slug = :slug,
              is_active = :is_active,
              color = :color,
              updated_at = NOW()
            WHERE
              id = :id
            "
        );

        $stmt->bindValue(':id', $this->id);
      }

      $stmt->bindValue(':name', $this->name);
      $stmt->bindValue(':slug', $this->slug);
      $stmt->bindValue(':is_active', $this->is_active, PDO::PARAM_BOOL);
      $stmt->bindValue(':color', $this->color);

      $result = $stmt->execute();
      if ($result) {
        return true;
      }

      return false;
    } catch (PDOException $e) {
      error_log("Category data before save: " . print_r([
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'is_active' => $this->is_active,
        'is_active_type' => gettype($this->is_active),
        'color' => $this->color
      ], true));

      error_log("Database error in Category::save(): " . $e->getMessage());

      return false;
    }
  }

  public function softDelete(): bool
  {
    if ($this->id === null) {
      return false;
    }
    // Check for active products or deals with this category. If there are, do not delete.
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = true");
    $stmt->execute([$this->id]);
    $result = $stmt->fetchColumn();
    if ($result > 0) {
      return false;
    }
    $stmt = $db->prepare("SELECT COUNT(*) FROM deals WHERE category_id = ? AND is_active = true");
    $stmt->execute([$this->id]);
    $result = $stmt->fetchColumn();
    if ($result > 0) {
      return false;
    }

    // If we made it here then we are all good to delete the category
    $stmt = $db->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = ?");

    return $stmt->execute([$this->id]);
  }

  // Getters and setters
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

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'slug' => $this->slug,
      'is_active' => $this->is_active,
      'color' => $this->color,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at
    ];
  }
}
