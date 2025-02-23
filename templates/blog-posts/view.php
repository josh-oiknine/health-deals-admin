<!-- Preview Blog Post -->
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h1><?= $blogPost['title'] ?></h1>
      <h4>By: <?= $blogPost['user_name'] ?></h4>
      <div class="blog-post-content">
        <?= $blogPost['body'] ?> <!-- HTML body -->
      </div>
    </div>
  </div>
</div>
