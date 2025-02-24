<?php

// Build the base URL with current filters
$baseUrl = '?';
$urlParts = array_filter([
  !empty($filters['status']) ? 'status=' . urlencode($filters['status']) : null,
  !empty($filters['job_type']) ? 'job_type=' . urlencode($filters['job_type']) : null,
  !empty($filters['product_id']) ? 'product_id=' . urlencode($filters['product_id']) : null,
]);
$baseUrl .= implode('&', $urlParts);
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Scraping Jobs</h1>
    
    <div class="d-flex align-items-center">
      <span class="badge bg-primary me-2 py-2 px-3">Pending: <?= $pendingJobsCount ?></span>
      <span class="badge bg-info me-2 py-2 px-3">Running: <?= $runningJobsCount ?></span>
      <span class="badge bg-success me-2 py-2 px-3">Completed: <?= $completedJobsCount ?></span>
      <span class="badge bg-danger me-2 py-2 px-3">Failed: <?= $failedJobsCount ?></span>
    </div>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-auto">
          <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="running" <?= ($filters['status'] ?? '') === 'running' ? 'selected' : '' ?>>Running</option>
            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
            <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
          </select>
        </div>
        <div class="col-md-auto">
          <select name="job_type" class="form-select">
            <option value="">All Types</option>
            <option value="hourly" <?= ($filters['job_type'] ?? '') === 'hourly' ? 'selected' : '' ?>>Hourly</option>
            <option value="daily" <?= ($filters['job_type'] ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="weekly" <?= ($filters['job_type'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
          </select>
        </div>
        <div class="col-md-auto ms-md-auto">
          <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped padded">
          <thead>
            <tr>
              <th>Product</th>
              <th>Type</th>
              <th>Status</th>
              <th>Queued</th>
              <th>Started</th>
              <th>Completed</th>
              <th>Processing Time</th>
              <th>Error</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="align-middle">
            <?php if (empty($jobs)): ?>
              <tr>
                <td colspan="8" class="text-center">No jobs found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($jobs as $job): ?>
                <tr>
                  <td>
                    <a href="/products/<?= $job['product_id'] ?>" class="text-decoration-none">
                      <?= htmlspecialchars(mb_strlen($job['product_name']) > 52 ? mb_substr($job['product_name'], 0, 52) . '...' : $job['product_name']) ?>
                    </a>
                  </td>
                  <td><?= htmlspecialchars($job['job_type']) ?></td>
                  <td>
                    <span class="badge bg-<?= getStatusBadgeClass($job['status']) ?>">
                      <?= ucfirst($job['status']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($job['celery_task_started_at']): ?>
                      <?= date('Y-m-d', strtotime($job['celery_task_started_at'])) ?><br>
                      <span class="ms-5"><?= date('H:i:s', strtotime($job['celery_task_started_at'])) ?></span>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($job['started_at']): ?>
                      <?= date('Y-m-d', strtotime($job['started_at'])) ?><br>
                      <span class="ms-5"><?= date('H:i:s', strtotime($job['started_at'])) ?></span>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($job['completed_at']): ?>
                      <?= date('Y-m-d', strtotime($job['completed_at'])) ?><br>
                      <span class="ms-5"><?= date('H:i:s', strtotime($job['completed_at'])) ?></span>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($job['completed_at']): ?>
                      <?php
                        if ($job['celery_task_started_at'] && $job['completed_at']) {
                          $processingTime = strtotime($job['completed_at']) - strtotime($job['celery_task_started_at']);
                          $hours = floor($processingTime / 3600);
                          $minutes = floor(($processingTime % 3600) / 60);
                          $seconds = $processingTime % 60;
                        } else {
                          $processingTime = strtotime($job['completed_at']) - strtotime($job['started_at']);
                          $hours = floor($processingTime / 3600);
                          $minutes = floor(($processingTime % 3600) / 60);
                          $seconds = $processingTime % 60;
                        }
                      ?>
                      <?= sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) ?>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($job['error_message']): ?>
                      <span class="text-danger" 
                            data-bs-toggle="tooltip" 
                            data-bs-placement="top" 
                            title="<?= htmlspecialchars($job['error_message']) ?>">
                        <i class="bi bi-exclamation-circle"></i>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="btn-group">
                      <button type="button" 
                          class="btn btn-sm btn-outline-danger"
                          onclick="stopJob(<?= $job['id'] ?>)">
                          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sign-stop" viewBox="0 0 16 16">
                            <path d="M3.16 10.08c-.931 0-1.447-.493-1.494-1.132h.653c.065.346.396.583.891.583.524 0 .83-.246.83-.62 0-.303-.203-.467-.637-.572l-.656-.164c-.61-.147-.978-.51-.978-1.078 0-.706.597-1.184 1.444-1.184.853 0 1.386.475 1.436 1.087h-.645c-.064-.32-.352-.542-.797-.542-.472 0-.77.246-.77.6 0 .261.196.437.553.522l.654.161c.673.164 1.06.487 1.06 1.11 0 .736-.574 1.228-1.544 1.228Zm3.427-3.51V10h-.665V6.57H4.753V6h3.006v.568H6.587Z"/>
                            <path fill-rule="evenodd" d="M11.045 7.73v.544c0 1.131-.636 1.805-1.661 1.805-1.026 0-1.664-.674-1.664-1.805V7.73c0-1.136.638-1.807 1.664-1.807s1.66.674 1.66 1.807Zm-.674.547v-.553c0-.827-.422-1.234-.987-1.234-.572 0-.99.407-.99 1.234v.553c0 .83.418 1.237.99 1.237.565 0 .987-.408.987-1.237m1.15-2.276h1.535c.82 0 1.316.55 1.316 1.292 0 .747-.501 1.289-1.321 1.289h-.865V10h-.665zm1.436 2.036c.463 0 .735-.272.735-.744s-.272-.741-.735-.741h-.774v1.485z"/>
                            <path fill-rule="evenodd" d="M4.893 0a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146A.5.5 0 0 0 11.107 0zM1 5.1 5.1 1h5.8L15 5.1v5.8L10.9 15H5.1L1 10.9z"/>
                          </svg>
                      </button>
                      <form id="stopForm<?= $job['id'] ?>" 
                          action="/scraping-jobs/stop/<?= $job['id'] ?>" 
                          method="POST" 
                          style="display: none;">
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php include __DIR__ . '/../components/pagination.php'; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

function stopJob(jobId) {
  if (confirm('Are you sure you want to stop this job?')) {
    document.getElementById('stopForm' + jobId).submit();
  }
}
</script>

<?php
function getStatusBadgeClass($status) {
  switch ($status) {
    case 'pending':
      return 'warning';
    case 'running':
      return 'info';
    case 'completed':
      return 'success';
    case 'failed':
      return 'danger';
    default:
      return 'secondary';
  }
}
?> 