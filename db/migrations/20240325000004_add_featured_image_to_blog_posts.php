<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddFeaturedImageToBlogPosts extends AbstractMigration
{
  public function change(): void
  {
    $this->table('blog_posts')
      ->addColumn('featured_image_url', 'string', ['limit' => 255, 'null' => true])
      ->update();
  }
} 