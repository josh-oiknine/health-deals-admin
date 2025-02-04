<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RemoveUrlFromCategories extends AbstractMigration
{
  public function change(): void
  {
    $this->table('categories')
      ->removeColumn('url')
      ->update();
  }
}
