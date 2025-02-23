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

$baseUrl = '?' . http_build_query(array_filter([
  'keyword' => $filters['keyword'] ?? null,
  'is_published' => $filters['is_published'] ?? null,
  'user_id' => $filters['user_id'] ?? null,
  'sort_by' => $filters['sort_by'] ?? $sortBy,
  'sort_order' => $filters['sort_order'] ?? $sortOrder
]));
?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Blog Posts</h1>
    <a href="/blog-posts/add" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> Add Blog Post
    </a>
  </div>

  <!-- Filters -->
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-md">
          <input type="text" name="keyword" class="form-control" 
                 placeholder="Search by title or keywords" 
                 value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
        </div>
        <div class="col-md-auto">
          <select name="is_published" class="form-select">
            <option value="">All Status</option>
            <option value="1" <?= isset($filters['is_published']) && $filters['is_published'] == '1' ? 'selected' : '' ?>>Published</option>
            <option value="0" <?= isset($filters['is_published']) && $filters['is_published'] == '0' ? 'selected' : '' ?>>Draft</option>
          </select>
        </div>
        <?php if ($currentUserEmail === 'josh@udev.com'): ?>
        <div class="col-md-auto">
          <select name="user_id" class="form-select">
            <option value="">All Authors</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= $user['id'] ?>" 
                <?= ($filters['user_id'] ?? '') == $user['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>
        <div class="col-md-auto">
          <button type="submit" class="btn btn-primary">
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
                <a href="<?= getSortUrl($baseUrl, 'title', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Title <?= getSortIcon($sortBy, 'title', $sortOrder) ?>
                </a>
              </th>
              <th>Body</th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'created_at', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Created <?= getSortIcon($sortBy, 'created_at', $sortOrder) ?>
                </a>
              </th>
              <th>
                <a href="<?= getSortUrl($baseUrl, 'published_at', $sortBy, $sortOrder) ?>" class="text-decoration-none text-dark">
                  Published <?= getSortIcon($sortBy, 'published_at', $sortOrder) ?>
                </a>
              </th>
              <th>Author</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($blogPosts)): ?>
              <tr>
                <td colspan="5" class="text-center">No blog posts found</td>
              </tr>
            <?php else: ?>
              <?php foreach ($blogPosts as $post): ?>
                <tr>
                  <td>
                    <?= htmlspecialchars($post['title']) ?>
                    <br>
                    <small class="text-muted">
                        <?= htmlspecialchars($post['slug']) ?>
                        <a href="#" onclick="copyToClipboard('https://www.yourhealthydeals.com/blog/<?= htmlspecialchars($post['slug']) ?>')"><i class="bi bi-clipboard"></i></a>
                    </small>
                  </td>
                  <td>
                    <?php
                      $bodyText = strip_tags($post['body']);
                      $shortBody = mb_strimwidth($bodyText, 0, 180, '...');
                      $shortBodyLines = explode("\n", wordwrap($shortBody, 90, "\n"));
                      echo htmlspecialchars(implode("\n", array_slice($shortBodyLines, 0, 2)));
                    ?>
                  </td>
                  <td><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></td>
                  <td>
                    <?php if ($post['published_at']): ?>
                      <span class="badge bg-success">Published</span>
                      <br>
                      <small><?= date('Y-m-d H:i', strtotime($post['published_at'])) ?></small>
                    <?php else: ?>
                      <span class="badge bg-warning">Draft</span>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($post['user_name'] ?? 'N/A') ?></td>
                  <td>
                    <div class="btn-group">
                      <a href="/blog-posts/edit/<?= $post['id'] ?>" 
                         class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="#" 
                         class="btn btn-sm btn-outline-primary" 
                         data-post-id="<?= $post['id'] ?>"
                         onclick="viewPost(<?= $post['id'] ?>)">
                        <i class="bi bi-eye"></i>
                      </a>
                      <button type="button" 
                              class="btn btn-sm btn-outline-danger"
                              onclick="deletePost(<?= $post['id'] ?>)">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                    <form id="deleteForm<?= $post['id'] ?>" 
                          action="/blog-posts/delete/<?= $post['id'] ?>" 
                          method="POST" 
                          style="display: none;">
                    </form>
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

<!-- Preview Modal -->
<div class="modal fade" 
     id="previewModal" 
     tabindex="-1" 
     aria-labelledby="previewModalLabel" 
     aria-hidden="true"
     style="padding-top: 80px;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 mb-3">
            <strong>Preview:</strong>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12 blog-post-container">
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
function deletePost(postId) {
  if (confirm('Are you sure you want to delete this blog post?')) {
    document.getElementById('deleteForm' + postId).submit();
  }
}

function viewPost(postId) {
  // Make an AJAX request to fetch the post content
  // Dispaly the content in a modal
  fetch(`/blog-posts/view/${postId}`)
    .then(response => response.text())
    .then(html => {
      const modal = document.getElementById('previewModal');
      modal.querySelector('.blog-post-container').innerHTML = html;
      const modalInstance = new bootstrap.Modal(modal);
      modalInstance.show();
    });
}
</script> 