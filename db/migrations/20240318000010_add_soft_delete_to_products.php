<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddSoftDeleteToProducts extends AbstractMigration
{
  public function change(): void
  {
    $this->table('products')
      ->addColumn('deleted_at', 'datetime', [
        'null' => true,
        'after' => 'updated_at',
        'comment' => 'Soft delete timestamp'
      ])
      ->update();

    // Add an index to improve performance of soft delete queries
    $this->table('products')
      ->addIndex(['deleted_at'])
      ->update();
  }
}
