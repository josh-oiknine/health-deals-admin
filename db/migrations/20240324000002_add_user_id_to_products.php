<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUserIdToProducts extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('products');
        $table->addColumn('user_id', 'integer', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', [
                  'delete' => 'SET_NULL',
                  'update' => 'NO_ACTION'
              ])
              ->update();
    }
} 