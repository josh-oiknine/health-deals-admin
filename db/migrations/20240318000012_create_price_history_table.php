<?php

use Phinx\Migration\AbstractMigration;

class CreatePriceHistoryTable extends AbstractMigration
{
    public function change(): void
    {
        // Create table first
        $table = $this->table('price_history', ['id' => true]);
        $table->addColumn('product_id', 'integer', ['null' => false])
            ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false])
            ->addColumn('created_at', 'datetime', ['null' => true])
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->save();
            
        // Add foreign key separately
        $table->addForeignKey('product_id', 'products', 'id', ['delete' => 'CASCADE'])
            ->save();
            
        // Add index separately
        $table->addIndex(['product_id', 'created_at'])
            ->save();
    }
}