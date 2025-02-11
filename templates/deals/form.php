<?php
$isEdit = $mode === 'edit';
$title = $isEdit ? 'Edit Deal' : 'Add Deal';
?>

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
                    <h2 class="mb-0"><?= $title ?></h2>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="affiliate_url" class="form-label">Deal URL *</label>
                            <div class="input-group">
                                <input type="url" 
                                       class="form-control" 
                                       id="affiliate_url" 
                                       name="affiliate_url" 
                                       value="<?= htmlspecialchars($deal['affiliate_url'] ?? '') ?>"
                                       placeholder="https://example.com/product"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="fetchDealInfo">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <i class="bi bi-arrow-clockwise"></i> <span class="button-text">Fetch Info</span>
                                </button>
                            </div>
                            <div class="form-text">Enter the deal URL and click Fetch Info to auto-populate fields</div>
                        </div>

                        <div class="mb-3">
                            <label for="store_id" class="form-label">Store *</label>
                            <select class="form-select" id="store_id" name="store_id" required>
                                <option value="">Select a store</option>
                                <?php foreach ($stores as $store): ?>
                                    <option value="<?= $store['id'] ?>" 
                                        <?= ($deal['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($store['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a store.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?= htmlspecialchars($deal['title'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback">
                                Please provide a title.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" 
                                     id="description" 
                                     name="description" 
                                     rows="3" 
                                     required><?= htmlspecialchars($deal['description'] ?? '') ?></textarea>
                            <div class="invalid-feedback">
                                Please provide a description.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="image_url" 
                                   name="image_url" 
                                   value="<?= htmlspecialchars($deal['image_url'] ?? '') ?>">
                            <div class="form-text">URL of the product image</div>
                            <?php if (!empty($deal['image_url'])): ?>
                                <div class="mt-2">
                                    <img src="<?= htmlspecialchars($deal['image_url']) ?>" 
                                         alt="Product image" 
                                         class="img-thumbnail" 
                                         style="max-height: 150px;">
                                </div>
                            <?php endif; ?>
                            <div id="imagePreview" class="mt-2 d-none">
                                <img src="" alt="Image preview" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="product_id" 
                                   name="product_id" 
                                   value="<?= htmlspecialchars($deal['product_id'] ?? '') ?>">
                            <div class="form-text">Link this deal to an existing product (optional)</div>
                        </div>

                        <div class="mb-3">
                            <label for="original_price" class="form-label">Original Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="original_price" 
                                       name="original_price" 
                                       step="0.01" 
                                       min="0" 
                                       value="<?= htmlspecialchars($deal['original_price'] ?? '0.00') ?>" 
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a valid price.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="deal_price" class="form-label">Deal Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="deal_price" 
                                       name="deal_price" 
                                       step="0.01" 
                                       min="0" 
                                       value="<?= htmlspecialchars($deal['deal_price'] ?? '0.00') ?>" 
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
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                        <?= ($deal['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       <?= (!isset($deal['is_active']) || $deal['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="form-text">Inactive deals won't be visible to users</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_featured" 
                                       name="is_featured" 
                                       <?= (!empty($deal['is_featured'])) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">Featured Deal</label>
                            </div>
                            <div class="form-text">Featured deals appear in prominent positions</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/deals" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $isEdit ? 'Update' : 'Create' ?> Deal
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
    const button = document.getElementById('fetchDealInfo');
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

// Function to update image preview
function updateImagePreview(url) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (url) {
        img.src = url;
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
    }
}

// Handle image URL changes
document.getElementById('image_url').addEventListener('input', function(e) {
    updateImagePreview(e.target.value);
});

// Handle URL parsing and deal info fetching
document.getElementById('fetchDealInfo').addEventListener('click', async function() {
    const urlInput = document.getElementById('affiliate_url');
    const url = urlInput.value;
    
    if (!url) {
        showNotification('Please enter a deal URL', 'danger');
        return;
    }

    try {
        // Show loading state
        toggleFetchButtonLoading(true);
        
        // Make API call to fetch product info
        const response = await fetch(`/api/deals/fetch-info?url=${encodeURIComponent(url)}`);
        const data = await response.json();
        
        if (data.success) {
            // Populate form fields with fetched data
            document.getElementById('title').value = data.title || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('image_url').value = data.image_url || '';
            updateImagePreview(data.image_url);
            
            if (data.product) {
                document.getElementById('product_id').value = data.product.id;
                document.getElementById('store_id').value = data.product.store_id;
                document.getElementById('category_id').value = data.product.category_id || '';
                document.getElementById('original_price').value = data.product.regular_price;
            }
            
            showNotification('Product information fetched successfully', 'success');
        } else {
            showNotification(data.error || 'Failed to fetch product information', 'danger');
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