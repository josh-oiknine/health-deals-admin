<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Categories</h1>
        <a href="/categories/add" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Category
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Color</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No categories found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: <?= htmlspecialchars($category->getColor()) ?>">
                                            <?= htmlspecialchars($category->getName()) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($category->getSlug()) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 25px; height: 25px; border-radius: 4px; background-color: <?= htmlspecialchars($category->getColor()) ?>"></div>
                                            <code class="small text-dark"><?= htmlspecialchars($category->getColor()) ?></code>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($category->isActive()): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $category->getCreatedAt() ? date('Y-m-d', strtotime($category->getCreatedAt())) : '' ?></td>
                                    <td><?= $category->getUpdatedAt() ? date('Y-m-d', strtotime($category->getUpdatedAt())) : '' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/categories/edit/<?= $category->getId() ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
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