<!-- Preview Blog Post -->
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h1><?= $blogPost['title'] ?></h1>
      <h4>By: <?= $blogPost['user_name'] ?></h4>
      
      <?php if (!empty($blogPost['featured_image_url'])): ?>
        <div class="featured-image my-3">
          <img src="<?= htmlspecialchars($blogPost['featured_image_url']) ?>" 
               alt="<?= htmlspecialchars($blogPost['title']) ?>" 
               class="img-fluid rounded">
        </div>
      <?php endif; ?>
      
      <div class="blog-post-content mt-4 mb-4">
        <?= $blogPost['body'] ?> <!-- HTML body -->
      </div>
    </div>
  </div>
</div>
