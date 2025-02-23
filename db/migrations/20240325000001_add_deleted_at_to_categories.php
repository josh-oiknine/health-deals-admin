<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddDeletedAtToCategories extends AbstractMigration
{
    public function change(): void
    {
        $this->table('categories')
            ->addColumn('deleted_at', 'datetime', [
                'null' => true,
                'after' => 'updated_at'
            ])
            ->update();
    }
} 