<?php
$sortOrder = $sorting['sort_order'] ?? 'DESC';
$sortBy = $sorting['sort_by'] ?? 'created_at';

function getSortIcon($currentSortBy, $thisSortBy, $currentSortOrder) {
  if ($currentSortBy !== $thisSortBy) {
    return '<i class="bi bi-arrow-down-up text-muted"></i>';
  }
  return $currentSortOrder === 'ASC' 
    ? '<i class="bi bi-arrow-up"></i>' 
    : '<i class="bi bi-arrow-down"></i>';
}

function getSortUrl($baseUrl, $thisSortBy, $currentSortBy, $currentSortOrder) {
  $newOrder = ($thisSortBy === $currentSortBy && $currentSortOrder === 'ASC') ? 'DESC' : 'ASC';
  return $baseUrl . '&sort_by=' . $thisSortBy . '&sort_order=' . $newOrder;
}

// Build the base URL with current filters

// echo '<pre>';
// var_dump(isset($filters['category_id']));
// echo '</pre>';
// die();

$baseUrl = '?';
$urlParts = array_filter([
  !empty($filters['keyword']) ? 'keyword=' . urlencode($filters['keyword']) : null,
  !empty($filters['store_id']) ? 'store_id=' . $filters['store_id'] : null,
  array_key_exists('category_id', $filters) ? 'category_id=' . ($filters['category_id'] === 0 ? 'none' : $filters['category_id']) : null,
  array_key_exists('is_active', $filters) ? 'is_active=' . ($filters['is_active'] ? '1' : '0') : null
]);
$baseUrl .= implode('&', $urlParts);
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <a href="/products/add" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> Add Product
    </a>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md-3">
          <input type="text" name="keyword" class="form-control" 
               placeholder="Search by name, SKU or price" 
               value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </div>
        <div class="col-md-2">
          <select name="store_id" class="form-select">
            <option value="">All Stores</option>
            <?php foreach ($stores as $store): ?>
              <option value="<?= $store['id'] ?>" 
                <?= ($filters['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($store['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="category_id" class="form-select">
            <option value="">All Categories</option>
            <option value="0" <?= ($filters['category_id'] ?? '') == '0' ? 'selected' : '' ?>>No Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>" 
                <?= ($filters['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <select name="is_active" class="form-select">
            <option value="" <?= isset($filters['is_active']) && $filters['is_active'] == '' ? 'selected' : '' ?>>All Status</option>
            <option value="1" <?= isset($filters['is_active']) && $filters['is_active'] == '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= isset($filters['is_active']) && $filters['is_active'] == '0' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-sm btn-primary me-2">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Name <?= getSortIcon($sortBy, 'name', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'store_name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Store <?= getSortIcon($sortBy, 'store_name', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'sku', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  SKU <?= getSortIcon($sortBy, 'sku', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'regular_price', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Regular Price <?= getSortIcon($sortBy, 'regular_price', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'category_name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Category <?= getSortIcon($sortBy, 'category_name', $sortOrder) ?>
                </a>
              </th>
              <th>Status</th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'created_at', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Created <?= getSortIcon($sortBy, 'created_at', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'updated_at', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Updated <?= getSortIcon($sortBy, 'updated_at', $sortOrder) ?>
                </a>
              </th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($products)): ?>
              <tr>
                <td colspan="9" class="text-center">No products found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td>
                    <a href="<?= htmlspecialchars($product['url']) ?>" 
                    target="_blank"
                    class="text-decoration-none"
                    >
                    <?= htmlspecialchars($product['name']) ?>
                    <small><i class="bi bi-box-arrow-up-right ms-1"></i></small>
                    </a>
                  </td>
                  <td><?= htmlspecialchars($product['store_name'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></td>
                  <td>$<?= number_format($product['regular_price'], 2) ?></td>
                  <td>
                    <?php if (!empty($product['category_name'])): ?>
                      <span class="badge" style="background-color: <?= htmlspecialchars($product['category_color'] ?? '#6c757d') ?>">
                        <?= htmlspecialchars($product['category_name']) ?>
                      </span>
                    <?php else: ?>
                      <span class="text-muted">No Category</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($product['is_active']): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td><?= $product['created_at'] ? date('Y-m-d', strtotime($product['created_at'])) : '' ?></td>
                  <td><?= $product['updated_at'] ? date('Y-m-d', strtotime($product['updated_at'])) : '' ?></td>
                  <td>
                    <div class="btn-group">
                      <button type="button" 
                          class="btn btn-sm btn-outline-success"
                          onclick="window.location.href='/products/edit/<?= $product['id'] ?>'">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" 
                          class="btn btn-sm btn-outline-primary"
                          data-bs-toggle="modal" 
                          data-bs-target="#historyModal<?= $product['id'] ?>"
                          data-product-id="<?= $product['id'] ?>"
                          data-last-checked="<?= htmlspecialchars($product['last_checked'] ?? 'Never') ?>">
                        <i class="bi bi-clock-history"></i>
                      </button>
                      <button type="button" 
                          class="btn btn-sm btn-outline-danger"
                          onclick="deleteProduct(<?= $product['id'] ?>)">
                        <i class="bi bi-trash"></i>
                      </button>
                      <form id="deleteForm<?= $product['id'] ?>" 
                          action="/products/delete/<?= $product['id'] ?>" 
                          method="POST" 
                          style="display: none;">
                      </form>
                    </div>

                    <!-- History Modal -->
                    <div class="modal fade" 
                       id="historyModal<?= $product['id'] ?>" 
                       tabindex="-1" 
                       aria-labelledby="historyModalLabel<?= $product['id'] ?>" 
                       aria-hidden="true"
                       style="padding-top: 80px;">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="historyModalLabel<?= $product['id'] ?>">
                              Price History - <?= htmlspecialchars(strlen($product['name']) > 80 ? substr($product['name'], 0, 47) . '...' : $product['name']) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-12 mb-3">
                                <strong>Last Checked:</strong> 
                                <span class="last-checked-date">
                                  <?= $product['last_checked'] ? date('Y-m-d H:i:s', strtotime($product['last_checked'])) : 'Pending' ?>
                                </span>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-12 price-history-container text-center" data-product-id="<?= $product['id'] ?>">
                                <div class="spinner-border text-primary" role="status">
                                  <span class="visually-hidden">Loading...</span>
                                </div>
                              </div>
                            </div>

                          </div>
                          
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pagination['last_page'] > 1): ?>
      <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
          Showing <?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?> 
          to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> 
          of <?= $pagination['total'] ?> results
        </div>
        <nav aria-label="Page navigation">
          <ul class="pagination mb-0">
            <?php if ($pagination['current_page'] > 1): ?>
              <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $pagination['current_page'] - 1 ?>">
                  Previous
                </a>
              </li>
            <?php endif; ?>
            
            <?php
            $start = max(1, $pagination['current_page'] - 2);
            $end = min($pagination['last_page'], $pagination['current_page'] + 2);
            
            if ($start > 1): ?>
              <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>&page=1">1</a>
              </li>
              <?php if ($start > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start; $i <= $end; $i++): ?>
              <li class="page-item <?= $i === $pagination['current_page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            
            <?php if ($end < $pagination['last_page']): ?>
              <?php if ($end < $pagination['last_page'] - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
              <?php endif; ?>
              <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $pagination['last_page'] ?>">
                  <?= $pagination['last_page'] ?>
                </a>
              </li>
            <?php endif; ?>
            
            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
              <li class="page-item">
                <a class="page-link" href="<?= $baseUrl ?>&page=<?= $pagination['current_page'] + 1 ?>">
                  Next
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div> 

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Load price history when modal is shown
  document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-product-id');
      const container = document.querySelector(`.price-history-container[data-product-id="${productId}"]`);
      
      // Fetch price history
      fetch(`/products/history/${productId}`)
        .then(response => response.text())
        .then(html => {
          container.innerHTML = html;
        })
        .catch(error => {
          container.innerHTML = '<div class="alert alert-danger">Error loading price history</div>';
          console.error('Error:', error);
        });
    });
  });
});

function deleteProduct(productId) {
  if (confirm('Are you sure you want to delete this product?')) {
    document.getElementById('deleteForm' + productId).submit();
  }
}
</script> 