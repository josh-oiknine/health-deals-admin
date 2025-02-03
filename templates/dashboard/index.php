<div class="container-fluid py-4">
    <div class="row">
        <!-- Metric Cards -->
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
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-store opacity-10"></i>
                            </div>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Active Products</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $metrics['activeProducts'] ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-box opacity-10"></i>
                            </div>
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
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="fas fa-tags opacity-10"></i>
                            </div>
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold text-muted">Messages Today</p>
                                <h5 class="font-weight-bolder mb-0">
                                    <?= $metrics['messagesSentToday'] ?>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-envelope opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Messages Sent (Last 24 Hours)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart" style="height: 300px;">
                        <!-- TODO: Add Chart.js integration for message statistics -->
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                            <i class="fas fa-chart-line me-2"></i> Message statistics coming soon
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Latest Deals</h6>
                </div>
                <div class="card-body p-3">
                    <!-- TODO: Add latest deals list -->
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-tags me-2"></i> Latest deals coming soon
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add required CSS for icons and gradients -->
<style>
.icon-shape {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.75rem;
}
.bg-gradient-primary { background: linear-gradient(145deg, #4e73df 0%, #224abe 100%); }
.bg-gradient-success { background: linear-gradient(145deg, #1cc88a 0%, #13855c 100%); }
.bg-gradient-warning { background: linear-gradient(145deg, #f6c23e 0%, #dda20a 100%); }
.bg-gradient-info { background: linear-gradient(145deg, #36b9cc 0%, #258391 100%); }
.icon-shape i { color: white; font-size: 1.25rem; }
.border-radius-md { border-radius: 0.5rem; }
</style> 