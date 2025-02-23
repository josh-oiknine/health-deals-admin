<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Users</h1>
        <a href="/users/add" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add User
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No users found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                    </td>
                                    <td>
                                        <?php if ($user['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $user['created_at'] ? date('Y-m-d', strtotime($user['created_at'])) : '' ?></td>
                                    <td><?= $user['updated_at'] ? date('Y-m-d', strtotime($user['updated_at'])) : '' ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/users/edit/<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="/users/remove-mfa/<?= $user['id'] ?>" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to remove 2FA from this user?');">
                                                <button type="submit" class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                            </form>
                                            <form action="/users/delete/<?= $user['id'] ?>" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
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