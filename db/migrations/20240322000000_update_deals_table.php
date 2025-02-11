<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateDealsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('deals');

        // Add new columns
        $table->addColumn('category_id', 'integer', ['null' => true, 'after' => 'product_id'])
              ->addColumn('original_price', 'decimal', ['precision' => 10, 'scale' => 2, 'after' => 'deal_price'])
              ->addForeignKey('category_id', 'categories', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
              ->addIndex(['category_id'])
              ->update();
    }
}