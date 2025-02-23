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
                                        <span class="badge" style="background-color: <?= htmlspecialchars($category['color']) ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($category['slug']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 25px; height: 25px; border-radius: 4px; background-color: <?= htmlspecialchars($category['color']) ?>"></div>
                                            <code class="small text-dark"><?= htmlspecialchars($category['color']) ?></code>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($category['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $category['created_at'] ? date('Y-m-d', strtotime($category['created_at'])) : '' ?></td>
                                    <td><?= $category['updated_at'] ? date('Y-m-d', strtotime($category['updated_at'])) : '' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/categories/edit/<?= $category['id'] ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                class="btn btn-sm btn-outline-danger delete-category" 
                                                data-category-id="<?= $category['id'] ?>"
                                                onclick="deleteCategory(<?= $category['id'] ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="deleteForm<?= $category['id'] ?>" 
                                                action="/categories/delete/<?= $category['id'] ?>" 
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
        </div>
    </div>
</div> 

<script>
function deleteCategory(categoryId) {
  if (confirm('Are you sure you want to delete this category?')) {
    document.getElementById('deleteForm' + categoryId).submit();
  }
}
</script>   