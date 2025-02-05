<div class="container-fluid py-4">
  <div class="row">
    <!-- Metric Cards -->
    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Active Products</p>
                <h5 class="font-weight-bolder mb-0">
                  <?= $metrics['activeProducts'] ?>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <a href="/products" class="text-decoration-none">
                <div class="icon icon-shape bg-primary text-center">
                  <i class="bi bi-box"></i>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Active Categories</p>
                <h5 class="font-weight-bolder mb-0">
                  <?= $metrics['activeCategories'] ?>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <a href="/categories" class="text-decoration-none">
                <div class="icon icon-shape bg-warning text-center">
                  <i class="bi bi-tags"></i>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Active Stores</p>
                <h5 class="font-weight-bolder mb-0">
                  <?= $metrics['activeStores'] ?>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <a href="/stores" class="text-decoration-none">
                <div class="icon icon-shape bg-success text-center">
                  <i class="bi bi-shop"></i>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body p-3">
          <div class="row">
            <div class="col-8">
              <div class="numbers">
                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Outbox</p>
                <h5 class="font-weight-bolder mb-0">
                  <?= $metrics['messagesSentToday'] ?>
                </h5>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-info text-center">
                <i class="bi bi-envelope"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row mt-4">
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
                    <th class="text-end">Original</th>
                    <th class="text-end">Current</th>
                    <th class="text-end">Savings</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($latestDeals as $index => $deal): ?>
                    <?php 
                      $savings = (($deal['regular_price'] - $deal['current_price']) / $deal['regular_price']) * 100;
                      $savingsFormatted = number_format($savings, 1);
                    ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td>
                        <a href="<?= $deal['url'] ?>" 
                           target="_blank" 
                           class="text-decoration-none text-reset">
                          <?= $deal['name'] ?>
                          <small><i class="bi bi-box-arrow-up-right"></i></small>
                        </a>
                      </td>
                      <td class="text-end">
                        $<?= number_format($deal['regular_price'], 2) ?>
                      </td>
                      <td class="text-end">
                        $<?= number_format($deal['current_price'], 2) ?>
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