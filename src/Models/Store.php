<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

class Store
{
  private $id = null;
  private $name;
  private $logo_url;
  private $url;
  private $is_active;
  private $created_at;
  private $updated_at;
  private $deleted_at;

  public function __construct(
    string $name = '',
    ?string $logo_url = null,
    ?string $url = null,
    bool $is_active = true
  ) {
    $this->name = $name;
    $this->logo_url = $logo_url;
    $this->url = $url;
    $this->is_active = $is_active;
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      error_log("Executing findAll query");
      $stmt = $db->prepare("SELECT * FROM stores WHERE deleted_at IS NULL ORDER BY name");
      $stmt->execute();
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $stores = [];
      foreach ($rows as $row) {
        $store = new self();
        $store->initFromArray($row);
        $stores[] = $store;
      }

      error_log("FindAll results: " . print_r($stores, true));

      return $stores;
    } catch (PDOException $e) {
      error_log("Database error in Store::findAll(): " . $e->getMessage());

      return [];
    }
  }

  public static function findAllActive(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT * FROM stores 
        WHERE deleted_at IS NULL 
        AND is_active = true 
        ORDER BY name
      ");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in Store::findAllActive(): " . $e->getMessage());
      return [];
    }
  }

  public static function findById(int $id): ?self
  {
    try {
      $db = Database::getInstance()->getConnection();
      error_log("Finding store with ID: " . $id);
      $stmt = $db->prepare("SELECT * FROM stores WHERE id = ? AND deleted_at IS NULL");
      $stmt->execute([$id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row) {
        $store = new self();
        $store->initFromArray($row);
        error_log("FindById result: " . print_r($store, true));

        return $store;
      }

      return null;
    } catch (PDOException $e) {
      error_log("Database error in Store::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function findByDomain(string $url): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      error_log("Finding store with URL: " . $url);
      $stmt = $db->prepare("SELECT * FROM stores WHERE url LIKE ? AND deleted_at IS NULL LIMIT 1");
      $stmt->execute(['%'.$url.'%']);
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
      error_log("Attempting to save store with name: " . $this->name);
      error_log("Store data before save: " . print_r([
        'id' => $this->id,
        'name' => $this->name,
        'logo_url' => $this->logo_url,
        'url' => $this->url,
        'is_active' => $this->is_active,
        'is_active_type' => gettype($this->is_active)
      ], true));

      if ($this->id === null) {
        $stmt = $db->prepare(
          "INSERT INTO stores (name, logo_url, url, is_active) 
                     VALUES (?, ?, ?, ?)"
        );
        $result = $stmt->execute([$this->name, $this->logo_url, $this->url, (int)$this->is_active]);
        if ($result) {
          $this->id = (int)$db->lastInsertId();
          error_log("Successfully inserted store with ID: " . $this->id);

          return true;
        }
        error_log("Failed to insert store");

        return false;
      } else {
        $stmt = $db->prepare(
          "UPDATE stores 
                     SET name = ?, logo_url = ?, url = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP 
                     WHERE id = ? AND deleted_at IS NULL"
        );
        $params = [$this->name, $this->logo_url, $this->url, (int)$this->is_active, $this->id];
        error_log("Update params: " . print_r($params, true));
        $result = $stmt->execute($params);
        if ($result) {
          error_log("Successfully updated store with ID: " . $this->id);

          return true;
        }
        error_log("Failed to update store");

        return false;
      }
    } catch (PDOException $e) {
      error_log("Database error in Store::save(): " . $e->getMessage());
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
    $stmt = $db->prepare("UPDATE stores SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");

    return $stmt->execute([$this->id]);
  }

  public static function countActive(): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("SELECT COUNT(*) FROM stores WHERE is_active = true AND deleted_at IS NULL");
      $stmt->execute();

      return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
      error_log("Database error in Store::countActive(): " . $e->getMessage());

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

  private function initFromArray(array $data): void
  {
    $this->id = isset($data['id']) ? (int)$data['id'] : null;
    $this->name = $data['name'] ?? '';
    $this->logo_url = $data['logo_url'] ?? null;
    $this->url = $data['url'] ?? null;
    $this->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $this->created_at = $data['created_at'] ?? null;
    $this->updated_at = $data['updated_at'] ?? null;
    $this->deleted_at = $data['deleted_at'] ?? null;
  }
}
