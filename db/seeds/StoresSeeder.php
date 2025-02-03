<?php

use Phinx\Seed\AbstractSeed;

class StoresSeeder extends AbstractSeed
{
  public function run(): void
  {
    $data = [
      [
        'name' => 'Amazon',
        'logo_url' => 'https://img.logo.dev/amazon.com?token=pk_BItyJ-OPQR2skfIDI-whLQ&size=100&retina=true',
        'url' => 'https://amazon.com',
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ],
      [
        'name' => 'Goop',
        'logo_url' => 'https://img.logo.dev/goop.com?token=pk_BItyJ-OPQR2skfIDI-whLQ&size=100&retina=true',
        'url' => 'https://goop.com',
        'is_active' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ],
      [
        'name' => 'Walmart',
        'logo_url' => 'https://img.logo.dev/walmart.com?token=pk_BItyJ-OPQR2skfIDI-whLQ&size=100&retina=true',
        'url' => 'https://walmart.com',
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]
    ];

    $this->table('stores')->insert($data)->save();
  }
} 