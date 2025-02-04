<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Products</h1>
        <a href="/products/add" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Store</th>
                            <th>SKU</th>
                            <th>Regular Price</th>
                            <th>Category</th>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
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
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['store_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></td>
                                    <td>$<?= number_format($product['regular_price'], 2) ?></td>
                                    <td>
                                        <?php if (!empty($product['category_name'])): ?>
                                            <span class="badge bg-info">
                                                <?= htmlspecialchars($product['category_name']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No Category</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['url']): ?>
                                            <a href="<?= htmlspecialchars($product['url']) ?>" 
                                               target="_blank"
                                               class="text-decoration-none">
                                                View Product
                                                <i class="bi bi-box-arrow-up-right ms-1"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No URL</span>
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
                                            <a href="/products/edit/<?= $product['id'] ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="/products/delete/<?= $product['id'] ?>" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this product?');">
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