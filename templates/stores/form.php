<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> Store</h2>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?= htmlspecialchars($store->getName()) ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a store name.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="url" 
                                   name="url" 
                                   value="<?= htmlspecialchars($store->getUrl() ?? '') ?>"
                                   placeholder="https://example.com">
                            <div class="form-text">The store's website URL</div>
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label">Logo URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="logo_url" 
                                   name="logo_url" 
                                   value="<?= htmlspecialchars($store->getLogoUrl() ?? '') ?>"
                                   placeholder="https://example.com/logo.png">
                            <div class="form-text">Direct URL to the store's logo image</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       <?= $store->isActive() ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="form-text">Inactive stores won't be visible to users</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/stores" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $isEdit ? 'Update' : 'Create' ?> Store
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
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script> 