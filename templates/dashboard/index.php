<div class="container-fluid py-4">
  <div class="row dashboard-row">
    <!-- Products -->
    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted text-sm-left d-flex">
                  <a href="/products" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-primary text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-box" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Products
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['activeProducts'] ?>
                  <small class="fs-5 text-muted">(<?= $metrics['inactiveProducts'] ?>)</small>
                </h3>
              </div>
            </div>
            <div class="col-4 text-end">
              <div style="height: 50px; position: relative;">
                <canvas id="productsChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Deals -->
    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  <a href="/deals" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-danger text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-tag" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Deals
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['activeDeals'] ?>
                  <small class="fs-5 text-muted">(<?= $metrics['inactiveDeals'] ?>)</small>
                </h3>
              </div>
            </div>
            <div class="col-4 text-end">
              <div style="height: 50px; position: relative;">
                <canvas id="dealsChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Outbox -->
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  <a href="/outbox" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-info text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-envelope" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Outbox
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['messagesSentToday'] ?>
                </h3>
              </div>
            </div>
            <div class="col-4 text-end">
              <!-- Chart will go here -->
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Blog Posts -->
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  <a href="/blog-posts" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-success text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-file-earmark-text" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Blog Posts
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['activeBlogPosts'] ?>
                  <small class="fs-5 text-muted">(<?= $metrics['draftBlogPosts'] ?>)</small>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stores and Categories -->
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  <a href="/stores" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-secondary text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-shop" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Stores: <strong><?= $metrics['activeStores'] ?></strong>
                </p>
                <p class="text-sm mb-0 text-muted d-flex mt-3">
                  <a href="/categories" class="text-decoration-none me-1">
                    <span class="icon icon-shape bg-secondary text-center" style="width: 1.5rem; height: 1.5rem;">
                      <i class="bi bi-tags" style="font-size: 0.75rem;"></i>
                    </span>
                  </a>
                  Categories: <strong><?= $metrics['activeCategories'] ?></strong>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Scrapping Jobs -->
  <div class="row mt-4 dashboard-row">
  
  <div class="col-xl-4 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body p-3">  
        <h3>Scraping Jobs</h3>
        <p class="text-sm mb-0 text-muted d-flex">
          Counts of Jobs in the scrapping queue
        </p>
      </div>
    </div>
  </div>
  <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  Pending
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['pendingJobsCount'] ?>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  Running
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['runningJobsCount'] ?>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  Completed
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['completedJobsCount'] ?>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-2 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-muted d-flex">
                  Failed
                </p>
                <h3 class="font-weight-bolder m-2 mb-1">
                  <?= $metrics['failedJobsCount'] ?>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>


  <!-- Charts Row -->
  <div class="row mt-4">

    <!-- Messages Sent -->
    <div class="col-lg-6 mb-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6>Messages Sent (Last 24 Hours)</h6>
        </div>
        <div class="card-body p-3">
          <div class="chart" style="height: 300px;">
            <!-- TODO: Add Chart.js integration for message statistics -->
            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
              <i class="bi bi-graph-up me-2"></i> Message statistics coming soon
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Latest Deals -->
    <div class="col-lg-6 mb-4">
      <div class="card">
        <div class="card-header pb-0">
          <h6>Latest Deals</h6>
        </div>
        <div class="card-body p-3">
          <?php if (empty($latestDeals)): ?>
            <div class="text-center text-muted py-4">
              <i class="bi bi-tags me-2"></i> No deals found
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Featured</th>
                    <th class="text-end">Original</th>
                    <th class="text-end">Current</th>
                    <th class="text-end">Discount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($latestDeals as $index => $deal): ?>
                    <?php 
                      $savings = (($deal['original_price'] - $deal['deal_price']) / $deal['original_price']) * 100;
                      $savingsFormatted = number_format($savings, 1);

                      $expiredPriceClass = '';
                      if ($deal['is_expired']):
                        $expiredPriceClass = 'text-decoration-line-through';
                      endif;
                    ?>
                    <tr
                      data-deal-id="<?= $deal['id'] ?>"
                      data-store-id="<?= $deal['store_id'] ?>"
                      data-product-id="<?= $deal['product_id'] ?>"
                      data-category-id="<?= $deal['category_id'] ?>"
                      data-description="<?= $deal['description'] ?>"
                      data-image-url="<?= $deal['image_url'] ?>"
                      data-created-at="<?= $deal['created_at'] ?>"
                    >
                      <td>
                        <a href="/deals?keyword=<?= $deal['product_sku'] ?>">
                          <?= $index + 1 ?>
                        </a>
                      </td>
                      <td>
                        <a href="<?= $deal['affiliate_url'] ?>" 
                           target="_blank" 
                           class="text-reset d-inline-block text-truncate text-decoration-none"
                           style="max-width: 260px;"
                        >
                          <?= $deal['title'] ?>
                        </a>
                      </td>
                      <td class="text-center">
                        <?= $deal['is_active'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?>
                        <?= !$deal['is_active'] ? '<i class="bi bi-dash-circle-fill text-danger"></i>' : '' ?>
                        <?= $deal['is_featured'] ? '<i class="bi bi-star-fill text-warning"></i>' : '' ?>
                        <?= !$deal['is_featured'] ? '<i class="bi bi-star text-muted"></i>' : '' ?>
                      </td>
                      <td class="text-end">
                        $<?= number_format($deal['original_price'], 2) ?>
                      </td>
                      <td class="text-end">
                        <span class="<?= $expiredPriceClass ?>">
                          $<?= number_format($deal['deal_price'], 2) ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <span class="badge bg-success">
                          <?= $savingsFormatted ?>%
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const chartConfig = {
    type: 'line',
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          enabled: false
        }
      },
      scales: {
        x: {
          display: false,
          grid: {
            display: false,
            drawBorder: false
          }
        },
        y: {
          display: false,
          grid: {
            display: false,
            drawBorder: false
          },
          min: 0,
          suggestedMax: 10
        }
      },
      elements: {
        line: {
          borderWidth: 1.5,
          tension: 0.4
        },
        point: {
          radius: 0
        }
      }
    }
  };

  // Products Chart
  const productsCtx = document.getElementById('productsChart').getContext('2d');
  new Chart(productsCtx, {
    ...chartConfig,
    data: {
      labels: <?= json_encode(array_keys($metrics['productsPerDay'])) ?>,
      datasets: [{
        data: <?= json_encode(array_values($metrics['productsPerDay'])) ?>,
        borderColor: '#5e72e4',
        backgroundColor: 'rgba(94, 114, 228, 0.1)',
        fill: true
      }]
    }
  });

  // Deals Chart
  const dealsCtx = document.getElementById('dealsChart').getContext('2d');
  new Chart(dealsCtx, {
    ...chartConfig,
    data: {
      labels: <?= json_encode(array_keys($metrics['dealsPerDay'])) ?>,
      datasets: [{
        data: <?= json_encode(array_values($metrics['dealsPerDay'])) ?>,
        borderColor: '#f5365c',
        backgroundColor: 'rgba(245, 54, 92, 0.1)',
        fill: true
      }]
    }
  });
});
</script> 