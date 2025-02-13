<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdateDealsTableAddIsExpired extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('deals');

    // Add new columns
    $table->addColumn('is_expired', 'boolean', ['default' => false, 'after' => 'is_featured'])
      ->update();
  }
}
