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
  array_key_exists('is_active', $filters) ? 'is_active=' . ($filters['is_active'] ? '1' : '0') : null,
  'sort_by' => $filters['sort_by'] ?? $sortBy,
  'sort_order' => $filters['sort_order'] ?? $sortOrder
]);
$baseUrl .= implode('&', $urlParts);
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Deals</h1>
    <a href="/deals/add" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> Add Deal
    </a>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md">
          <input type="text" name="keyword" class="form-control" 
            placeholder="Search by title, description or coupon" 
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
        <div class="col-md-auto ms-md-auto">
          <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Deals Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped padded">
          <thead>
            <tr>
              <th>Image</th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'title', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Title <?= getSortIcon($sortBy, 'title', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'store_name', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Store <?= getSortIcon($sortBy, 'store_name', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'original_price', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Original Price <?= getSortIcon($sortBy, 'original_price', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'deal_price', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Deal Price <?= getSortIcon($sortBy, 'deal_price', $sortOrder) ?>
                </a>
              </th>
              <th>Discount</th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'is_featured', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Featured <?= getSortIcon($sortBy, 'is_featured', $sortOrder) ?>
                </a>
              </th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="align-middle">
            <?php if (empty($deals)): ?>
              <tr>
                <td colspan="10" class="text-center">No deals found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($deals as $deal): ?>
                <tr>
                  <?php 
                    $expiredClass = '';
                    $expiredPriceClass = '';
                    if ($deal['is_expired']):
                      $expiredClass = 'grayscale-overlay';
                      $expiredPriceClass = 'text-decoration-line-through';
                    endif;
                    
                    $savings = (($deal['original_price'] - $deal['deal_price']) / $deal['original_price']) * 100;
                    $savingsFormatted = number_format($savings, 1);
                  ?>
                  <td>
                    <?php if (!empty($deal['image_url'])): ?>
                      <div class="<?= $expiredClass ?>">
                        <img src="<?= htmlspecialchars($deal['image_url']) ?>" 
                          alt="<?= htmlspecialchars($deal['title']) ?>"
                          class="img-thumbnail"
                          style="max-height: 52px; width: auto;">
                      </div>
                    <?php else: ?>
                      <div class="text-muted small">No image</div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <a href="<?= htmlspecialchars($deal['affiliate_url']) ?>" 
                      target="_blank"
                      class="text-decoration-none"
                    >
                      <?= htmlspecialchars(mb_strlen($deal['title']) > 52 ? mb_substr($deal['title'], 0, 52) . '...' : $deal['title']) ?>
                      <small><i class="bi bi-box-arrow-up-right ms-1"></i></small>
                    </a>
                    <?php if (!empty($deal['category_name'])): ?>
                      <br>
                      <span class="badge" style="background-color: <?= htmlspecialchars($deal['category_color'] ?? '#6c757d') ?>">
                        <?= htmlspecialchars($deal['category_name']) ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($deal['store_name'] ?? 'N/A') ?>
                  </td>
                  <td>
                    $<?= number_format($deal['original_price'], 2) ?>
                  </td>
                  <td>
                    <span class="<?= $expiredPriceClass ?>">
                      $<?= number_format($deal['deal_price'], 2) ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <span class="badge bg-success">
                      <?= $savingsFormatted ?>%
                    </span>
                  </td>
                  <td>
                    <?php if ($deal['is_featured']): ?>
                      <span class="badge bg-warning text-dark">
                        <i class="bi bi-star-fill"></i> Featured
                      </span>
                    <?php else: ?>
                      <span class="badge bg-light text-dark">
                        <i class="bi bi-star"></i> Regular
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($deal['is_active']): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="btn-group">
                      <a href="/products?keyword=<?= $deal['product_sku'] ?>" 
                         class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i>
                      </a>
                      <a href="/deals/edit/<?= $deal['id'] ?>" 
                         class="btn btn-sm btn-outline-success">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <button type="button" 
                              class="btn btn-sm btn-outline-danger delete-deal" 
                              data-deal-id="<?= $deal['id'] ?>"
                              data-click-count="0">
                        <i class="bi bi-trash"></i>
                      </button>
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
  // Get all delete buttons
  const deleteButtons = document.querySelectorAll('.delete-deal');
  
  // Add click event listener to each button
  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      const dealId = this.getAttribute('data-deal-id');
      let clickCount = parseInt(this.getAttribute('data-click-count'));
      
      // Increment click count
      clickCount++;
      this.setAttribute('data-click-count', clickCount);
      
      if (clickCount === 1) {
        // First click - change icon to question mark
        this.innerHTML = '<i class="bi bi-question-lg"></i>';
        
        // Reset after 3 seconds if second click doesn't happen
        setTimeout(() => {
          if (parseInt(this.getAttribute('data-click-count')) === 1) {
            this.innerHTML = '<i class="bi bi-trash"></i>';
            this.setAttribute('data-click-count', '0');
          }
        }, 3000);
      } else if (clickCount === 2) {
        // Second click - show confirmation dialog
        if (confirm('Are you sure you want to delete this deal? This will also delete all price history for the matching product.')) {
          fetch(`/deals/delete/${dealId}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            }
          }).then(response => {
            if (response.ok) {
              window.location.reload();
            } else {
              alert('Failed to delete the deal.');
              this.innerHTML = '<i class="bi bi-trash"></i>';
              this.setAttribute('data-click-count', '0');
            }
          }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the deal.');
            this.innerHTML = '<i class="bi bi-trash"></i>';
            this.setAttribute('data-click-count', '0');
          });
        } else {
          // Reset button if user cancels
          this.innerHTML = '<i class="bi bi-trash"></i>';
          this.setAttribute('data-click-count', '0');
        }
      }
    });
  });
});
</script> 