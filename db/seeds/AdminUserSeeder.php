<?php

use Phinx\Seed\AbstractSeed;

class AdminUserSeeder extends AbstractSeed
{
  public function run(): void
  {
    $data = [
      [
        'email' => 'josh@udev.com',
        'password' => password_hash('S0mething@', PASSWORD_DEFAULT),
        'first_name' => 'Josh',
        'last_name' => 'Oiknine',
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'totp_secret' => null,
        'totp_setup_complete' => false
      ],
      [
        'email' => 'shaneeoiknine@gmail.com',
        'password' => password_hash('newPassword7^', PASSWORD_DEFAULT),
        'first_name' => 'Shanee',
        'last_name' => 'Oiknine',
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
        'totp_secret' => null,
        'totp_setup_complete' => false
      ],
    ];

    $this->table('users')->insert($data)->save();
  }
}
