<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Health Deals Admin</title>

  <!-- Apple Touch Icon -->
  <link rel="apple-touch-icon" sizes="57x57" href="/assets/images/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="/assets/images/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="/assets/images/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/images/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="/assets/images/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/assets/images/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="/assets/images/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/assets/images/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-icon-180x180.png">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="192x192"  href="/assets/images/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="/assets/images/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
  <link rel="manifest" href="/assets/images/manifest.json">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
  <?php
  $isLoginPage = $_SERVER['REQUEST_URI'] === '/';
  $isMFAPage = $_SERVER['REQUEST_URI'] === '/mfa';
  $isSetup2FAPage = $_SERVER['REQUEST_URI'] === '/setup-2fa';
  $isVerifyMFAPage = $_SERVER['REQUEST_URI'] === '/verify-mfa';
  $hasAuthToken = isset($_COOKIE['auth_token']);
  ?>

  <?php if ($isLoginPage || !$hasAuthToken || $isMFAPage || $isSetup2FAPage || $isVerifyMFAPage): ?>
    <?= $content ?>
  <?php else: ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <button id="sidebarToggle" class="btn btn-dark">
          <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand ms-3" href="/dashboard">Your Healthy Deals Admin</a>
      </div>
    </nav>

    <div class="sidebar" id="sidebar">
      <div class="position-sticky">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '' ?>" href="/dashboard" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
              <i class="bi bi-speedometer2 me-2"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/products' ? 'active' : '' ?>" href="/products" data-bs-toggle="tooltip" data-bs-placement="right" title="Products">
              <i class="bi bi-box me-2"></i>
              <span>Products</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/deals' ? 'active' : '' ?>" href="/deals" data-bs-toggle="tooltip" data-bs-placement="right" title="Deals">
              <i class="bi bi-percent me-2"></i>
              <span>Deals</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/outbox' ? 'active' : '' ?>" href="/outbox" data-bs-toggle="tooltip" data-bs-placement="right" title="Outbox">
              <i class="bi bi-envelope me-2"></i>
              <span>Outbox</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/stores' ? 'active' : '' ?>" href="/stores" data-bs-toggle="tooltip" data-bs-placement="right" title="Stores">
              <i class="bi bi-shop me-2"></i>
              <span>Stores</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/categories' ? 'active' : '' ?>" href="/categories" data-bs-toggle="tooltip" data-bs-placement="right" title="Categories">
              <i class="bi bi-tags me-2"></i>
              <span>Categories</span>
            </a>
          </li>
          <div class="divider"></div>
          
          <li class="nav-item">
            <a class="nav-link <?= $_SERVER['REQUEST_URI'] === '/settings' ? 'active' : '' ?>" href="/settings" data-bs-toggle="tooltip" data-bs-placement="right" title="Settings">
              <i class="bi bi-gear me-2"></i>
              <span>Settings</span>
            </a>
          </li>
          <li class="nav-item">
            <form action="/logout" method="POST" class="nav-link">
              <button type="submit" class="btn btn-link text-decoration-none p-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                <i class="bi bi-box-arrow-right me-2"></i>
                <span>Logout</span>
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>

    <main id="content" class="py-4">
      <div class="container-fluid pt-5">
        <?= $content ?>
      </div>
    </main>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/assets/js/app.js"></script>
</body>
</html> 