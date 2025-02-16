<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSoftDeleteToUsers extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('deleted_at', 'timestamp', ['null' => true])
              ->update();
    }
} 