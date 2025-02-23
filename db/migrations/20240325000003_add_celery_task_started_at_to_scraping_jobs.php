<?php

use Phinx\Migration\AbstractMigration;

class AddCeleryTaskStartedAtToScrapingJobs extends AbstractMigration
{
  public function change(): void
  {
    $this->table('scraping_jobs')
      ->addColumn('celery_task_started_at', 'datetime', [
        'null' => true,
        'after' => 'status'
      ])
      ->update();
  }
} 