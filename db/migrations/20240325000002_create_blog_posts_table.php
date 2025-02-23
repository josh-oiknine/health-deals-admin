<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateBlogPostsTable extends AbstractMigration
{
  public function change(): void
  {
    $this->table('blog_posts')
      ->addColumn('title', 'string', ['limit' => 255])
      ->addColumn('slug', 'string', ['limit' => 255])
      ->addColumn('body', 'text')
      ->addColumn('seo_keywords', 'string', ['limit' => 255, 'null' => true])
      ->addColumn('user_id', 'integer')
      ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
      ->addColumn('published_at', 'datetime', ['null' => true])
      ->addColumn('deleted_at', 'datetime', ['null' => true])
      ->addIndex(['slug'], ['unique' => true])
      ->addIndex(['user_id'])
      ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
      ->create();
  }
}
