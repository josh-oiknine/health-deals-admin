<?php

use Phinx\Seed\AbstractSeed;

class ProductsSeeder extends AbstractSeed
{
  public function run(): void
  {
    // Create a faker instance without using the url() method directly
    $faker = \Faker\Factory::create();
    
    // Get all store IDs
    $stores = $this->fetchAll('SELECT id FROM stores WHERE is_active = true');
    $storeIds = array_column($stores, 'id');
    
    // Get all category IDs
    $categories = $this->fetchAll('SELECT id FROM categories WHERE is_active = true');
    $categoryIds = array_column($categories, 'id');
    
    $products = [];
    
    // Domain list for generating URLs manually
    $domains = ['example.com', 'test.org', 'demo.net', 'sample.io', 'product.store'];
    
    for ($i = 0; $i < 25; $i++) {
      $name = $faker->words(rand(2, 5), true);
      $slug = strtolower(str_replace(' ', '-', $name)) . '-' . $faker->numberBetween(100, 999);
      $regularPrice = $faker->randomFloat(2, 10, 200);
      
      // Generate URL manually to avoid the Faker issue
      $domain = $domains[array_rand($domains)];
      $path = strtolower(str_replace(' ', '-', $name));
      $url = "https://www.{$domain}/products/{$path}";
      
      $products[] = [
        'name' => ucwords($name),
        'slug' => $slug,
        'url' => $url,
        'category_id' => $faker->randomElement($categoryIds),
        'store_id' => $faker->randomElement($storeIds),
        'regular_price' => $regularPrice,
        'sku' => $faker->bothify('??###???'),
        'upc' => $faker->numerify('############'),
        'is_active' => true,
        'user_id' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ];
    }
    
    $this->table('products')->insert($products)->saveData();
  }
} 