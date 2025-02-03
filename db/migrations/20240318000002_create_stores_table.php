<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStoresTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('stores', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('name', 'string', ['limit' => 255])
      ->addColumn('logo_url', 'string', ['limit' => 1024, 'null' => true])
      ->addColumn('is_active', 'boolean', ['default' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addIndex(['name'])
      ->create();
  }
}
