<div class="container-fluid">
    <!-- Toast container for notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="notificationToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $mode === 'edit' ? 'Edit' : 'Add' ?> Product</h2>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="url" class="form-label">Product URL *</label>
                            <div class="input-group">
                                <input type="url" 
                                       class="form-control" 
                                       id="url" 
                                       name="url" 
                                       value="<?= htmlspecialchars($product['url'] ?? '') ?>"
                                       placeholder="https://example.com/product"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="fetchProductInfo">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-arrow-clockwise"></i> <span class="button-text">Fetch Info</span>
                                </button>
                            </div>
                            <div class="form-text">Enter the product URL and click Fetch Info to auto-populate fields</div>
                        </div>

                        <div class="mb-3">
                            <label for="store_id" class="form-label">Store *</label>
                            <select class="form-select" id="store_id" name="store_id" required>
                                <option value="">Select a store</option>
                                <?php foreach ($stores as $store): ?>
                                    <option value="<?= $store->getId() ?>" 
                                        <?= ($product['store_id'] ?? '') == $store->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($store->getName()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a store.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($product['name'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback">
                                Please provide a product name.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="slug" 
                                   name="slug" 
                                   value="<?= htmlspecialchars($product['slug'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback">
                                Please provide a slug.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="sku" 
                                   name="sku" 
                                   value="<?= htmlspecialchars($product['sku'] ?? '') ?>">
                            <div class="form-text">For Amazon products, this will be the ASIN</div>
                        </div>

                        <div class="mb-3">
                            <label for="regular_price" class="form-label">Regular Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="regular_price" 
                                       name="regular_price" 
                                       value="<?= htmlspecialchars($product['regular_price'] ?? '0.00') ?>" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a valid price.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">Select a category</option>
                                <!-- Add categories here when implemented -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       <?= ($product['is_active'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="form-text">Inactive products won't be visible to users</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/products" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $mode === 'edit' ? 'Update' : 'Create' ?> Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
})()

// Function to create slug from text
function createSlug(text, maxLength = 100) {
  let slug = text
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_|_$/g, '');
  
  // If slug is longer than maxLength, trim it at the last underscore before maxLength
  if (slug.length > maxLength) {
    slug = slug.substr(0, maxLength);
    // Find the last underscore in the truncated string
    const lastUnderscore = slug.lastIndexOf('_');
    if (lastUnderscore !== -1) {
      slug = slug.substr(0, lastUnderscore);
    }
  }
  
  return slug;
}

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
  document.getElementById('slug').value = createSlug(e.target.value);
});

// Function to show toast notification
function showNotification(message, type = 'success') {
  const toast = document.getElementById('notificationToast');
  const toastBody = toast.querySelector('.toast-body');
  
  // Set toast color based on type
  toast.className = 'toast align-items-center text-white border-0';
  toast.classList.add(`bg-${type}`);
  
  // Set message
  toastBody.textContent = message;
  
  // Show toast using Bootstrap's Toast API
  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();
}

// Function to toggle loading state of fetch button
function toggleFetchButtonLoading(isLoading) {
  const button = document.getElementById('fetchProductInfo');
  const spinner = button.querySelector('.spinner-border');
  const icon = button.querySelector('.bi-arrow-clockwise');
  const text = button.querySelector('.button-text');
  
  button.disabled = isLoading;
  
  if (isLoading) {
    spinner.classList.remove('d-none');
    icon.classList.add('d-none');
    text.textContent = 'Fetching...';
  } else {
    spinner.classList.add('d-none');
    icon.classList.remove('d-none');
    text.textContent = 'Fetch Info';
  }
}

// Handle URL parsing and product info fetching
document.getElementById('fetchProductInfo').addEventListener('click', async function() {
    const urlInput = document.getElementById('url');
    const url = urlInput.value;
    
    if (!url) {
        showNotification('Please enter a product URL', 'danger');
        return;
    }

    try {
        // Example of parsing Amazon URL
        if (url.includes('amazon.com')) {
            // Extract ASIN from URL
            const asinMatch = url.match(/\/dp\/([A-Z0-9]{10})/);
            if (asinMatch) {
                const asin = asinMatch[1];
                
                // Show loading state
                toggleFetchButtonLoading(true);
                
                // Make API call to your backend endpoint
                const response = await fetch(`/api/products/fetch-info?url=${encodeURIComponent(url)}`);
                const responseData = await response.json();
                
                if (responseData.success) {
                    // Auto-select Amazon store
                    const storeSelect = document.getElementById('store_id');
                    Array.from(storeSelect.options).forEach(option => {
                        if (option.text.toLowerCase().includes('amazon')) {
                            option.selected = true;
                        }
                    });

                    // Populate other fields
                    const data = responseData.data;
                    document.getElementById('name').value = data.name;
                    document.getElementById('slug').value = createSlug(data.name);
                    document.getElementById('sku').value = asin;
                    document.getElementById('regular_price').value = data.price;
                    
                    showNotification('Product information fetched successfully', 'success');
                } else {
                    showNotification(responseData.error || 'Failed to fetch product information', 'danger');
                }
            } else {
                showNotification('Invalid Amazon product URL. Please make sure it contains a valid product ID.', 'danger');
            }
        } else {
            showNotification('Currently only Amazon URLs are supported for auto-fetching information.', 'warning');
        }
    } catch (error) {
        console.error('Error fetching product information:', error);
        showNotification('An error occurred while fetching product information', 'danger');
    } finally {
        // Always hide loading state when done
        toggleFetchButtonLoading(false);
    }
});
</script> 