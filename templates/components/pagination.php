<?php
/**
 * Pagination Component
 * 
 * @param array $pagination [
 *   'current_page' => int,
 *   'per_page' => int,
 *   'total' => int,
 *   'last_page' => int
 * ]
 * @param string $baseUrl The base URL for pagination links
 * @param array $params Additional URL parameters to maintain (sorting, filters, etc.) [optional]
 */

$existingParams = !empty($params) ? array_filter($params, fn($value) => $value !== null && $value !== '') : [];
$buildUrl = function($page) use ($baseUrl, $existingParams) {
  $urlParams = array_merge(['page' => $page], $existingParams);
  $queryString = http_build_query($urlParams);
  $baseUrl = rtrim($baseUrl, '&');
  return $baseUrl . (strpos($baseUrl, '?') === false ? '?' : '&') . $queryString;
};
?>

<div class="d-flex justify-content-between align-items-center mt-4">
  <div class="text-muted">
    Showing <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> 
    to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> 
    of <?= $pagination['total'] ?> results
  </div>

  <?php if ($pagination['last_page'] > 1): ?>
  <nav aria-label="Page navigation">
    <ul class="pagination mb-0">
      <!-- First Page -->
      <?php if ($pagination['current_page'] > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl(1) ?>" aria-label="First Page">
            <i class="bi bi-chevron-bar-left"></i>
          </a>
        </li>
      <?php endif; ?>

      <!-- Previous Page -->
      <?php if ($pagination['current_page'] > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl($pagination['current_page'] - 1) ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>
      <?php endif; ?>
      
      <?php
      // Calculate page range
      $range = 5;
      $start = max(1, $pagination['current_page'] - $range);
      $end = min($pagination['last_page'], $pagination['current_page'] + $range);
      
      // Show first page if we're not starting at 1
      if ($start > 1): ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl(1) ?>">1</a>
        </li>
        <?php if ($start > 2): ?>
          <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
      <?php endif; ?>
      
      <?php for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
          <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      
      <?php if ($end < $pagination['last_page']): ?>
        <?php if ($end < $pagination['last_page'] - 1): ?>
          <li class="page-item disabled"><span class="page-link">...</span></li>
        <?php endif; ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl($pagination['last_page']) ?>"><?= $pagination['last_page'] ?></a>
        </li>
      <?php endif; ?>

      <!-- Next Page -->
      <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl($pagination['current_page'] + 1) ?>">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      <?php endif; ?>

      <!-- Last Page -->
      <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
        <li class="page-item">
          <a class="page-link" href="<?= $buildUrl($pagination['last_page']) ?>" aria-label="Last Page">
            <i class="bi bi-chevron-bar-right"></i>
          </a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>