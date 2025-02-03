<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateOutboxTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('outbox', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('text_description', 'text')
      ->addColumn('html_description', 'text')
      ->addColumn('image_url', 'string', ['limit' => 1024, 'null' => true])
      ->addColumn('status', 'string', ['limit' => 20, 'default' => 'pending'])
      ->addColumn('error_data', 'json', ['null' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addIndex(['status', 'created_at'])
      ->create();
  }
}
