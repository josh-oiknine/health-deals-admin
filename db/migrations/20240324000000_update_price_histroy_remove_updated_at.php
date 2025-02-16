<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UpdatePriceHistroyRemoveUpdatedAt extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('price_history');

    // Add new columns
    $table->removeColumn('updated_at')
      ->update();
  }
}
