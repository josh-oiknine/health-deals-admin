<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateProductsTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('products', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('name', 'string', ['limit' => 255])
      ->addColumn('slug', 'string', ['limit' => 255])
      ->addColumn('url', 'string', ['limit' => 1024])
      ->addColumn('category_id', 'integer', ['null' => true])
      ->addColumn('store_id', 'integer', ['null' => false])
      ->addColumn('regular_price', 'decimal', ['precision' => 10, 'scale' => 2])
      ->addColumn('sku', 'string', ['limit' => 50, 'null' => true])
      ->addColumn('is_active', 'boolean', ['default' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addIndex(['slug'], ['unique' => true])
      ->addIndex(['sku'], ['unique' => true])
      ->addForeignKey('category_id', 'categories', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
      ->addForeignKey('store_id', 'stores', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
      ->create();
  }
}
