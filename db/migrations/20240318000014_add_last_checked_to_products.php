<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddLastCheckedToProducts extends AbstractMigration
{
    public function change(): void
    {
        $this->table('products')
            ->addColumn('last_checked', 'datetime', ['null' => true])
            ->update();
    }
} 