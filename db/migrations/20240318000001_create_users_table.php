<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('users', ['id' => 'id', 'primary_key' => ['id']]);
    $table->addColumn('email', 'string', ['limit' => 255])
      ->addColumn('password', 'string', ['limit' => 255])
      ->addColumn('first_name', 'string', ['limit' => 100])
      ->addColumn('last_name', 'string', ['limit' => 100])
      ->addColumn('mfa_secret', 'string', ['limit' => 32, 'null' => true])
      ->addColumn('last_mfa_at', 'timestamp', ['null' => true])
      ->addColumn('is_active', 'boolean', ['default' => true])
      ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
      ->addIndex(['email'], ['unique' => true])
      ->create();
  }
}
