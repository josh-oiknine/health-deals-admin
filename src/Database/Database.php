<?php

namespace App\Database;

use Exception;
use PDO;
use PDOException;

class Database
{
  private static $instance = null;
  private $connection;

  private function __construct()
  {
    try {
      $this->connection = new PDO(
        "pgsql:host=" . $_ENV['DB_HOST'] .
          ";port=" . $_ENV['DB_PORT'] .
          ";dbname=" . $_ENV['DB_NAME'],
        $_ENV['DB_USERNAME'],
        $_ENV['DB_PASSWORD']
      );

      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage());
    }
  }

  public static function getInstance(): self
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }

  // Prevent cloning of the instance
  private function __clone()
  {
  }

  // Prevent unserializing of the instance
  public function __wakeup()
  {
    throw new Exception("Cannot unserialize singleton");
  }
}
