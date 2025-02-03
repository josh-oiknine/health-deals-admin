<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddUrlAndSoftDeleteToStores extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('stores');
    $table->addColumn('url', 'string', ['limit' => 1024, 'null' => true])
      ->addColumn('deleted_at', 'timestamp', ['null' => true])
      ->update();
  }
}
