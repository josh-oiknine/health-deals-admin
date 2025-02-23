<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUpcToProducts extends AbstractMigration
{
  public function change(): void
  {
    $this->table('products')
      ->addColumn('upc', 'string', [
        'null' => true,
        'limit' => 12,
        'after' => 'sku'
      ])
      ->update();
  }
}
