<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateDealsTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('deals', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('store_id', 'integer')
      ->addColumn('product_id', 'integer')
      ->addColumn('title', 'string', ['limit' => 255])
      ->addColumn('description', 'text', ['null' => true])
      ->addColumn('deal_price', 'decimal', ['precision' => 10, 'scale' => 2])
      ->addColumn('image_url', 'string', ['limit' => 1024, 'null' => true])
      ->addColumn('affiliate_url', 'string', ['limit' => 1024])
      ->addColumn('is_featured', 'boolean', ['default' => false])
      ->addColumn('is_active', 'boolean', ['default' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addForeignKey('store_id', 'stores', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
      ->addForeignKey('product_id', 'products', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
      ->create();
  }
}
