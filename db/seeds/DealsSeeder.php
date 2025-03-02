<?php

use Phinx\Seed\AbstractSeed;

class DealsSeeder extends AbstractSeed
{
  public function run(): void
  {
    $faker = \Faker\Factory::create();
    
    // Get all store IDs
    $stores = $this->fetchAll('SELECT id FROM stores WHERE is_active = true');
    $storeIds = array_column($stores, 'id');
    
    // Get all product IDs
    $products = $this->fetchAll('SELECT id, regular_price, category_id FROM products WHERE is_active = true');
    
    // Get all category IDs
    $categories = $this->fetchAll('SELECT id FROM categories WHERE is_active = true');
    $categoryIds = array_column($categories, 'id');
    
    $deals = [];
    
    // Domain list for generating URLs manually
    $domains = ['example.com', 'test.org', 'demo.net', 'sample.io', 'deal.store'];
    
    // Sample descriptions for deals
    $descriptions = [
      "Limited time offer! Get this amazing product at a discounted price. Don't miss out on this incredible deal.",
      "Exclusive discount available now. This offer won't last long, so grab it while you can!",
      "Special promotion for our valued customers. High-quality product at an unbeatable price.",
      "Flash sale happening now! Save big on this popular item that everyone loves.",
      "Incredible savings on this must-have product. Perfect for gifts or treating yourself.",
      "Amazing deal on a top-rated product. Customers love this item for its quality and value.",
      "Huge discount available for a limited time. This is one of our best-selling products!",
      "Special offer you won't want to miss. This product has received excellent reviews from our customers.",
      "Fantastic savings on this premium product. Supplies are limited, so act fast!",
      "Unbeatable price on this customer favorite. A perfect addition to your collection."
    ];
    
    // Create 100 deals
    for ($i = 0; $i < 100; $i++) {
      // Randomly select a product
      $product = $faker->randomElement($products);
      
      // Calculate a discounted price (10-50% off)
      $discountPercent = $faker->numberBetween(10, 50);
      $originalPrice = $product['regular_price'];
      $dealPrice = round($originalPrice * (1 - ($discountPercent / 100)), 2);
      
      // Determine if this deal should be featured (25 featured, 75 not featured)
      $isFeatured = $i < 25;
      
      // Create deal title
      $dealTitles = [
        "Save {$discountPercent}% on",
        "Flash Sale:",
        "Limited Time Offer:",
        "Special Deal:",
        "Today's Deal:",
        "Weekend Special:",
        "Exclusive Offer:",
        "Hot Deal:",
        "Clearance:",
        "Best Value:"
      ];
      
      $productName = $this->fetchRow("SELECT name FROM products WHERE id = {$product['id']}")['name'];
      $title = $faker->randomElement($dealTitles) . " " . $productName;
      
      // Generate image URL manually
      $imageId = $faker->numberBetween(1, 1000);
      $imageUrl = "https://picsum.photos/id/{$imageId}/640/480";
      
      // Generate affiliate URL manually
      $domain = $domains[array_rand($domains)];
      $path = strtolower(str_replace(' ', '-', $productName));
      $affiliateUrl = "https://www.{$domain}/deals/{$path}?ref=affiliate";
      
      // Select a random description
      $description = $descriptions[array_rand($descriptions)];
      
      $deals[] = [
        'store_id' => $faker->randomElement($storeIds),
        'product_id' => $product['id'],
        'category_id' => $product['category_id'] ?? $faker->randomElement($categoryIds),
        'title' => $title,
        'description' => $description,
        'deal_price' => $dealPrice,
        'original_price' => $originalPrice,
        'image_url' => $imageUrl,
        'affiliate_url' => $affiliateUrl,
        'is_featured' => $isFeatured,
        'is_expired' => false,
        'is_active' => true,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ];
    }
    
    // Shuffle the deals to randomize which ones are featured
    shuffle($deals);
    
    // Set the first 25 deals as featured
    for ($i = 0; $i < 25; $i++) {
      $deals[$i]['is_featured'] = true;
    }
    
    // Set the rest as not featured
    for ($i = 25; $i < 100; $i++) {
      $deals[$i]['is_featured'] = false;
    }
    
    $this->table('deals')->insert($deals)->saveData();
  }
} 