<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Database;
use DateTime;
use PDO;
use PDOException;

class ScrapingJob
{
  private ?int $id = null;
  private int $product_id;
  private string $job_type;
  private string $status;
  private ?DateTime $celery_task_started_at = null;
  private ?DateTime $started_at = null;
  private ?DateTime $completed_at = null;
  private ?string $error_message = null;
  private ?string $celery_task_id = null;
  private ?DateTime $created_at = null;
  private ?DateTime $updated_at = null;
  private ?Product $product = null;

  public function __construct(
    int $product_id,
    string $job_type = 'on-demand',
    string $status = 'pending'
  ) {
    $this->product_id = $product_id;
    $this->job_type = $job_type;
    $this->status = $status;
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
          scraping_jobs
          LEFT JOIN products ON scraping_jobs.product_id = products.id
        WHERE
          1=1
      ";

      $params = [];

      // Apply filters
      if (!empty($filters['status'])) {
        $query .= " AND scraping_jobs.status = :status";
        $params['status'] = $filters['status'];
      }

      if (!empty($filters['job_type'])) {
        $query .= " AND scraping_jobs.job_type = :job_type";
        $params['job_type'] = $filters['job_type'];
      }

      if (!empty($filters['product_id'])) {
        $query .= " AND scraping_jobs.product_id = :product_id";
        $params['product_id'] = $filters['product_id'];
      }

      // Count total results
      $countQuery = str_replace("{fields}", "COUNT(*)", $query);
      $countStmt = $db->prepare($countQuery);
      $countStmt->execute($params);
      $total = (int)$countStmt->fetchColumn();

      // Apply sorting
      $allowedSortFields = [
        'created_at' => 'scraping_jobs.created_at',
        'started_at' => 'scraping_jobs.started_at',
        'completed_at' => 'scraping_jobs.completed_at',
        'status' => 'scraping_jobs.status',
        'job_type' => 'scraping_jobs.job_type'
      ];

      $sortField = $allowedSortFields[$sortBy] ?? 'scraping_jobs.created_at';
      $query .= " ORDER BY {$sortField} {$sortOrder}";

      // Apply pagination
      $offset = ($page - 1) * $perPage;
      $query .= " LIMIT :limit OFFSET :offset";

      $query = str_replace("{fields}", "scraping_jobs.*, products.name as product_name", $query);
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
      error_log("Database error in ScrapingJob::findFiltered(): " . $e->getMessage());

      return [
        'data' => [],
        'total' => 0,
        'per_page' => $perPage,
        'page' => $page,
        'last_page' => 1
      ];
    }
  }

  public static function findById(int $id): ?array
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT
          scraping_jobs.*,
          products.name as product_name
        FROM scraping_jobs
        LEFT JOIN products ON scraping_jobs.product_id = products.id
        WHERE scraping_jobs.id = :id
      ");
      $stmt->execute(['id' => $id]);
      $result = $stmt->fetch();

      return $result ?: null;
    } catch (PDOException $e) {
      error_log("Database error in ScrapingJob::findById(): " . $e->getMessage());

      return null;
    }
  }

  public static function findCountByStatus(string $status): int
  {
    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("
        SELECT COUNT(*)
        FROM scraping_jobs
        WHERE status = :status
      ");

      $stmt->bindValue(':status', $status, PDO::PARAM_STR);

      $stmt->execute();
      $result = $stmt->fetchColumn();

      if ($result === false) {
        error_log("No result found for status: " . $status);

        return 0;
      }

      return (int)$result;
    } catch (PDOException $e) {
      error_log("Database error in ScrapingJob::findCountByStatus(): " . $e->getMessage());

      return 0;
    }
  }

  public function save(): bool
  {
    try {
      $db = Database::getInstance()->getConnection();
      $now = date('Y-m-d H:i:s');

      if ($this->id === null) {
        $stmt = $db->prepare("
          INSERT INTO scraping_jobs (
            product_id, job_type, status, started_at, completed_at,
            error_message, celery_task_id, created_at, updated_at
          ) VALUES (
            :product_id, :job_type, :status, :started_at, :completed_at,
            :error_message, :celery_task_id, :created_at, :updated_at
          )
        ");

        $params = [
          'product_id' => $this->product_id,
          'job_type' => $this->job_type,
          'status' => $this->status,
          'started_at' => $this->started_at,
          'completed_at' => $this->completed_at,
          'error_message' => $this->error_message,
          'celery_task_id' => $this->celery_task_id,
          'created_at' => $now,
          'updated_at' => $now
        ];
      } else {
        $stmt = $db->prepare("
          UPDATE scraping_jobs SET
            product_id = :product_id,
            job_type = :job_type,
            status = :status,
            started_at = :started_at,
            completed_at = :completed_at,
            error_message = :error_message,
            celery_task_id = :celery_task_id,
            updated_at = :updated_at
          WHERE id = :id
        ");

        $params = [
          'id' => $this->id,
          'product_id' => $this->product_id,
          'job_type' => $this->job_type,
          'status' => $this->status,
          'started_at' => $this->started_at,
          'completed_at' => $this->completed_at,
          'error_message' => $this->error_message,
          'celery_task_id' => $this->celery_task_id,
          'updated_at' => $now
        ];
      }

      $result = $stmt->execute($params);

      if ($result && $this->id === null) {
        $this->id = (int)$db->lastInsertId();
      }

      return $result;
    } catch (PDOException $e) {
      error_log("Database error in ScrapingJob::save(): " . $e->getMessage());

      return false;
    }
  }

  public function stop(): bool
  {
    if ($this->id === null) {
      return false;
    }

    try {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare("UPDATE scraping_jobs SET status = 'stopped', error_message = 'Job stopped by user' WHERE id = :id");

      return $stmt->execute(['id' => $this->id]);
    } catch (PDOException $e) {
      error_log("Database error in ScrapingJob::stop(): " . $e->getMessage());

      return false;
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

  public function getJobType(): string
  {
    return $this->job_type;
  }
  public function setJobType(string $job_type): void
  {
    $this->job_type = $job_type;
  }

  public function getStatus(): string
  {
    return $this->status;
  }
  public function setStatus(string $status): void
  {
    $this->status = $status;
  }

  public function getStartedAt(): ?string
  {
    return $this->started_at;
  }
  public function setStartedAt(?string $started_at): void
  {
    $this->started_at = $started_at;
  }

  public function getCompletedAt(): ?string
  {
    return $this->completed_at;
  }
  public function setCompletedAt(?string $completed_at): void
  {
    $this->completed_at = $completed_at;
  }

  public function getErrorMessage(): ?string
  {
    return $this->error_message;
  }
  public function setErrorMessage(?string $error_message): void
  {
    $this->error_message = $error_message;
  }

  public function getCeleryTaskId(): ?string
  {
    return $this->celery_task_id;
  }
  public function setCeleryTaskId(?string $celery_task_id): void
  {
    $this->celery_task_id = $celery_task_id;
  }

  public function getCreatedAt(): ?string
  {
    return $this->created_at;
  }
  public function getUpdatedAt(): ?string
  {
    return $this->updated_at;
  }
}
