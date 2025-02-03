<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCategoriesTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('categories', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('name', 'string', ['limit' => 100])
      ->addColumn('slug', 'string', ['limit' => 100])
      ->addColumn('url', 'string', ['limit' => 1024])
      ->addColumn('is_active', 'boolean', ['default' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addIndex(['slug'], ['unique' => true])
      ->create();
  }
}
