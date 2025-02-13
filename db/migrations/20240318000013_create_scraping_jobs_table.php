<?php

use Phinx\Migration\AbstractMigration;

class CreateScrapingJobsTable extends AbstractMigration
{
  public function change(): void
  {
    $this->table('scraping_jobs', ['id' => true])
      ->addColumn('product_id', 'integer', ['null' => false])
      ->addColumn('job_type', 'string', [
        'limit' => 20,
        'null' => false,
        'default' => 'hourly'
      ])
      ->addColumn('status', 'string', [
        'limit' => 20,
        'null' => false,
        'default' => 'pending'
      ])
      ->addColumn('started_at', 'datetime', ['null' => true])
      ->addColumn('completed_at', 'datetime', ['null' => true])
      ->addColumn('error_message', 'text', ['null' => true])
      ->addColumn('celery_task_id', 'string', ['null' => true, 'limit' => 255])
      ->addColumn('created_at', 'datetime', ['null' => true])
      ->addColumn('updated_at', 'datetime', ['null' => true])
      ->addForeignKey('product_id', 'products', 'id', ['delete' => 'CASCADE'])
      ->addIndex(['status', 'job_type'])
      ->addIndex(['celery_task_id'])
      ->create();
  }
}
