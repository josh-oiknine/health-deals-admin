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
        <div class="col-md">
          <input type="text" name="keyword" class="form-control" 
               placeholder="Search by name, SKU, UPC or price" 
               value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </div>
        <div class="col-md-auto">
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
        <div class="col-md-auto">
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
        <div class="col-md-auto">
          <select name="is_active" class="form-select">
            <option value="" <?= isset($filters['is_active']) && $filters['is_active'] == '' ? 'selected' : '' ?>>All Status</option>
            <option value="1" <?= isset($filters['is_active']) && $filters['is_active'] == '1' ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= isset($filters['is_active']) && $filters['is_active'] == '0' ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
        <?php if ($currentUserEmail === 'josh@udev.com'): ?>
        <div class="col-md-auto">
          <select name="user_id" class="form-select">
            <option value="">All Users</option>
            <option value="0" <?= ($filters['user_id'] ?? '') === 0 ? 'selected' : '' ?>>N/A</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= $user->getId() ?>" 
                <?= ($filters['user_id'] ?? '') == $user->getId() ? 'selected' : '' ?>>
                <?= htmlspecialchars($user->getFirstName()) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
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
        <table class="table table-striped">
          <thead>
            <tr>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Name <?= getSortIcon($sortBy, 'name', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'sku', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  SKU <?= getSortIcon($sortBy, 'sku', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'regular_price', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Price <?= getSortIcon($sortBy, 'regular_price', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'category_name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Category <?= getSortIcon($sortBy, 'category_name', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'created_at', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Created <?= getSortIcon($sortBy, 'created_at', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'last_checked', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Last Checked <?= getSortIcon($sortBy, 'last_checked', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'user_id', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  User<?= getSortIcon($sortBy, 'user_id', $sortOrder) ?>
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
                    <div class="d-flex align-items-center">
                      <a href="<?= htmlspecialchars($product['url']) ?>" 
                         target="_blank"
                         class="text-decoration-none d-flex align-items-center"
                         data-bs-toggle="tooltip" 
                         data-bs-placement="top" 
                         title="<?= htmlspecialchars($product['name']) ?>"
                      >
                        <img src="<?= htmlspecialchars($product['store_logo_url'] ?? '/assets/images/favicon-32x32.png') ?>" 
                             alt="Product icon"
                             class="me-2"
                             style="width: 32px; height: 32px; object-fit: contain;"
                             onerror="this.src='/assets/images/favicon-16x16.png'">
                        <?= htmlspecialchars(mb_strlen($product['name']) > 52 ? mb_substr($product['name'], 0, 52) . '...' : $product['name']) ?>
                        <small><i class="bi bi-box-arrow-up-right ms-1"></i></small>
                      </a>
                      <button class="btn btn-sm btn-link text-decoration-none ms-2" 
                              onclick="copyToClipboard('<?= htmlspecialchars(addslashes($product['url'])) ?>')"
                              data-bs-toggle="tooltip"
                              data-bs-placement="top"
                              title="Copy URL to clipboard">
                        <i class="bi bi-clipboard"></i>
                      </button>
                    </div>
                  </td>
                  <td>
                    <?= htmlspecialchars($product['sku'] ?? 'N/A') ?><br>
                    <?php if (!empty($product['upc'])): ?>
                      <small class="text-muted">UPC: <?= htmlspecialchars($product['upc']) ?></small><br>
                    <?php endif; ?>
                  </td>
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
                    <?= $product['created_at'] ? date('Y-m-d', strtotime($product['created_at'])) : '' ?>
                    <br>
                    <?php if ($product['is_active']): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= $product['last_checked'] ? date('Y-m-d H:i:s', strtotime($product['last_checked'])) : 'Never' ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($product['user_first_name'] ?? 'N/A') ?>
                  </td>
                  <td>
                    <div class="btn-group">
                      <button type="button" 
                          class="btn btn-sm btn-outline-success"
                          onclick="window.location.href='/products/edit/<?= $product['id'] ?>'">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" 
                          class="btn btn-sm btn-outline-primary show-history"
                          data-bs-toggle="modal" 
                          data-bs-target="#historyModal"
                          data-product-id="<?= $product['id'] ?>"
                          data-product-name="<?= htmlspecialchars($product['name']) ?>"
                          data-last-checked="<?= $product['last_checked'] ? date('Y-m-d H:i:s', strtotime($product['last_checked'])) : 'Never' ?>">
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

<!-- History Modal -->
<div class="modal fade" 
     id="historyModal" 
     tabindex="-1" 
     aria-labelledby="historyModalLabel" 
     aria-hidden="true"
     style="padding-top: 80px;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="historyModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 mb-3">
            <strong>Last Checked:</strong> 
            <span class="last-checked-date"></span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 price-history-container text-center">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  const modal = document.getElementById('historyModal');
  const modalTitle = modal.querySelector('.modal-title');
  const lastCheckedSpan = modal.querySelector('.last-checked-date');
  const historyContainer = modal.querySelector('.price-history-container');

  // Handle history button clicks
  document.querySelectorAll('.show-history').forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-product-id');
      const productName = this.getAttribute('data-product-name');
      const lastChecked = this.getAttribute('data-last-checked');

      // Update modal title and last checked date
      modalTitle.textContent = (productName.length > 80 ? productName.substring(0, 77) + '...' : productName);
      lastCheckedSpan.textContent = lastChecked;

      // Load price history
      fetch(`/products/history/${productId}`)
        .then(response => response.text())
        .then(html => {
          historyContainer.innerHTML = html;
        })
        .catch(error => {
          historyContainer.innerHTML = '<div class="alert alert-danger">Error loading price history</div>';
          console.error('Error:', error);
        });
    });
  });

  // Clear history container when modal is hidden
  modal.addEventListener('hidden.bs.modal', function () {
    historyContainer.innerHTML = `
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    `;
  });
});

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    // Create and show a temporary tooltip
    const tooltip = bootstrap.Tooltip.getInstance(event.currentTarget);
    const originalTitle = event.currentTarget.getAttribute('data-bs-original-title');
    
    tooltip.setContent({ '.tooltip-inner': 'Copied!' });
    
    setTimeout(() => {
      tooltip.setContent({ '.tooltip-inner': originalTitle });
    }, 1000);
  }).catch(err => {
    console.error('Failed to copy text: ', err);
  });
}

function deleteProduct(productId) {
  if (confirm('Are you sure you want to delete this product?')) {
    document.getElementById('deleteForm' + productId).submit();
  }
}
</script> 