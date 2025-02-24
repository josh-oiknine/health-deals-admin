<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

class User
{
  private $id = null;
  private $email;
  private $password;
  private $first_name;
  private $last_name;
  private $is_active;
  private $created_at;
  private $updated_at;

  public function __construct(
    string $email = '',
    string $password = '',
    string $first_name = '',
    string $last_name = '',
    bool $is_active = true
  ) {
    $this->email = $email;
    $this->password = $password;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->is_active = $is_active;
  }

  public static function findAll(): array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM users 
         WHERE deleted_at IS NULL 
         ORDER BY email ASC"
      );
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      error_log("Database error in User::findAll(): " . $e->getMessage());

      return [];
    }
  }

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM users 
         WHERE id = ? 
         AND deleted_at IS NULL"
      );
      $stmt->execute([$id]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in User::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function findByEmail(string $email): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare(
        "SELECT * FROM users 
         WHERE email = ? 
         AND deleted_at IS NULL"
      );
      $stmt->execute([$email]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in User::findByEmail(): " . $e->getMessage());

      return null;
    }
  }

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();

      if ($this->id === null) {
        // Hash password for new users
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt = $db->prepare(
          "INSERT INTO users (email, password, first_name, last_name, is_active) 
           VALUES (?, ?, ?, ?, ?)"
        );
        $result = $stmt->execute([
          $this->email,
          $this->password,
          $this->first_name,
          $this->last_name,
          (int)$this->is_active
        ]);

        if ($result) {
          $this->id = (int)$db->lastInsertId();

          return true;
        }

        return false;
      } else {
        $updateFields = [];
        $params = [];

        // Only update password if it's been changed (not empty)
        if (!empty($this->password)) {
          $updateFields[] = "password = ?";
          $params[] = password_hash($this->password, PASSWORD_DEFAULT);
        }

        $updateFields = array_merge($updateFields, [
          "email = ?",
          "first_name = ?",
          "last_name = ?",
          "is_active = ?",
          "updated_at = NOW()"
        ]);

        $params = array_merge($params, [
          $this->email,
          $this->first_name,
          $this->last_name,
          (int)$this->is_active,
          $this->id
        ]);

        $stmt = $db->prepare(
          "UPDATE users 
           SET " . implode(", ", $updateFields) . "
           WHERE id = ? AND deleted_at IS NULL"
        );

        return $stmt->execute($params);
      }
    } catch (PDOException $e) {
      error_log("Database error in User::save(): " . $e->getMessage());

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
      $stmt = $db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");

      return $stmt->execute([$this->id]);
    } catch (PDOException $e) {
      error_log("Database error in User::softDelete(): " . $e->getMessage());

      return false;
    }
  }

  public function removeMfa(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("UPDATE users SET totp_secret = NULL, totp_setup_complete = FALSE, updated_at = NOW() WHERE id = ?");

      return $stmt->execute([$this->id]);
    } catch (PDOException $e) {
      error_log("Database error in User::removeMfa(): " . $e->getMessage());

      return false;
    }
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

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): void
  {
    $this->email = $email;
  }

  public function setPassword(string $password): void
  {
    $this->password = $password;
  }

  public function getFirstName(): string
  {
    return $this->first_name;
  }

  public function setFirstName(string $first_name): void
  {
    $this->first_name = $first_name;
  }

  public function getLastName(): string
  {
    return $this->last_name;
  }

  public function setLastName(string $last_name): void
  {
    $this->last_name = $last_name;
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
    $this->email = $data['email'] ?? '';
    $this->password = $data['password'] ?? '';
    $this->first_name = $data['first_name'] ?? '';
    $this->last_name = $data['last_name'] ?? '';
    $this->is_active = isset($data['is_active']) ? (bool)$data['is_active'] : true;
    $this->created_at = $data['created_at'] ?? null;
    $this->updated_at = $data['updated_at'] ?? null;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'email' => $this->email,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'is_active' => $this->is_active,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
      'totp_setup_complete' => $this->totp_setup_complete
    ];
  }
}
