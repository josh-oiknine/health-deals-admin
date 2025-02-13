<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColorToCategories extends AbstractMigration
{
  public function change(): void
  {
    $this->table('categories')
      ->addColumn('color', 'string', [
        'null' => true,
        'limit' => 7,
        'default' => '#6c757d',
        'comment' => 'Hex color code for category badge'
      ])
      ->update();
  }
}
