<?php

use Phinx\Seed\AbstractSeed;

class AdminUserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'email'      => 'josh@udev.com',
                'password'   => password_hash('S0mething@', PASSWORD_DEFAULT),
                'first_name' => 'Josh',
                'last_name'  => 'Oiknine',
                'is_active'  => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->table('users')->insert($data)->save();
    }
} 