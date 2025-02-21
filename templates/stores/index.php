<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Stores</h1>
        <a href="/stores/add" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Store
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($stores)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No stores found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($stores as $store): ?>
                                <tr>
                                    <td>
                                        <?php if ($store['logo_url']): ?>
                                            <img src="<?= htmlspecialchars($store['logo_url']) ?>" 
                                                alt="<?= htmlspecialchars($store['name']) ?>" 
                                                class="store-logo"
                                                style="max-width: 50px; max-height: 50px;">
                                        <?php else: ?>
                                            <div class="text-muted">No logo</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($store['name']) ?></td>
                                    <td>
                                        <?php if ($store['url']): ?>
                                            <a href="<?= htmlspecialchars($store['url']) ?>" 
                                               target="_blank"
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($store['url']) ?>
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No URL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($store['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $store['created_at'] ? date('Y-m-d', strtotime($store['created_at'])) : '' ?></td>
                                    <td><?= $store['updated_at'] ? date('Y-m-d', strtotime($store['updated_at'])) : '' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/stores/edit/<?= $store['id'] ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="/stores/delete/<?= $store['id'] ?>" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this store?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>