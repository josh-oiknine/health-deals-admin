<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use PDO;

class Product
{
    private $id = null;
    private $store_id;
    private $name;
    private $description;
    private $price;
    private $sale_price;
    private $image_url;
    private $product_url;
    private $is_active;
    private $created_at;
    private $updated_at;
    private $deleted_at;
    private $store = null;

    public function __construct(
        string $name = '',
        string $slug = '',
        string $url = '',
        ?int $category_id = null,
        int $store_id = 0,
        float $regular_price = 0.0,
        ?string $sku = null,
        bool $is_active = true
    ) {
        $this->name = $name;
        $this->slug = $slug;
        $this->url = $url;
        $this->category_id = $category_id;
        $this->store_id = $store_id;
        $this->regular_price = $regular_price;
        $this->sku = $sku;
        $this->is_active = $is_active;
    }

    public static function findAll(): array
    {
        $db = Database::getInstance();
        $stmt = $db->query("
            SELECT p.*, s.name as store_name 
            FROM products p 
            LEFT JOIN stores s ON p.store_id = s.id 
            WHERE s.deleted_at IS NULL
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public static function findByStore(int $store_id): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT * FROM products 
            WHERE store_id = :store_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT p.*, s.name as store_name 
            FROM products p 
            LEFT JOIN stores s ON p.store_id = s.id 
            WHERE p.id = :id AND s.deleted_at IS NULL
        ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function save(): bool
    {
        $db = Database::getInstance();
        
        if ($this->id === null) {
            $stmt = $db->prepare("
                INSERT INTO products (name, slug, url, category_id, store_id, regular_price, sku, is_active)
                VALUES (:name, :slug, :url, :category_id, :store_id, :regular_price, :sku, :is_active)
            ");
        } else {
            $stmt = $db->prepare("
                UPDATE products 
                SET name = :name, 
                    slug = :slug,
                    url = :url,
                    category_id = :category_id,
                    store_id = :store_id,
                    regular_price = :regular_price,
                    sku = :sku,
                    is_active = :is_active,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");
            $stmt->bindValue(':id', $this->id);
        }

        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':slug', $this->slug);
        $stmt->bindValue(':url', $this->url);
        $stmt->bindValue(':category_id', $this->category_id);
        $stmt->bindValue(':store_id', $this->store_id);
        $stmt->bindValue(':regular_price', $this->regular_price);
        $stmt->bindValue(':sku', $this->sku);
        $stmt->bindValue(':is_active', $this->is_active, \PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public static function delete(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function softDelete(): bool
    {
        if ($this->id === null) {
            return false;
        }
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE products SET deleted_at = CURRENT_TIMESTAMP WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (\PDOException $e) {
            error_log("Database error in Product::softDelete(): " . $e->getMessage());
            return false;
        }
    }

    public static function countActive(): int
    {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE is_active = true AND deleted_at IS NULL");
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Database error in Product::countActive(): " . $e->getMessage());
            return 0;
        }
    }

    public function getStore(): ?Store
    {
        if ($this->store === null && $this->store_id) {
            $this->store = Store::findById($this->store_id);
        }
        return $this->store;
    }

    // Getters and setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }
    
    public function getUrl(): string { return $this->url; }
    public function setUrl(string $url): void { $this->url = $url; }
    
    public function getCategoryId(): ?int { return $this->category_id; }
    public function setCategoryId(?int $category_id): void { $this->category_id = $category_id; }
    
    public function getStoreId(): int { return $this->store_id; }
    public function setStoreId(int $store_id): void { $this->store_id = $store_id; }
    
    public function getRegularPrice(): float { return $this->regular_price; }
    public function setRegularPrice(float $regular_price): void { $this->regular_price = $regular_price; }
    
    public function getSku(): ?string { return $this->sku; }
    public function setSku(?string $sku): void { $this->sku = $sku; }
    
    public function getIsActive(): bool { return $this->is_active; }
    public function setIsActive(bool $is_active): void { $this->is_active = $is_active; }
} 