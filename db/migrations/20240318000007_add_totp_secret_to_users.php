<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddTotpSecretToUsers extends AbstractMigration
{
  public function change(): void
  {
    $this->table('users')
      ->addColumn('totp_secret', 'string', ['null' => true])
      ->addColumn('totp_setup_complete', 'boolean', ['default' => false])
      ->removeColumn('mfa_secret') // Remove the old MFA secret field
      ->update();
  }
}
