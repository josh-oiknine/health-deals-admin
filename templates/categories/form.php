<div class="container-fluid">
    <style>
        .color-preset {
            border: 2px solid transparent;
            padding: 0;
            transition: all 0.2s;
            cursor: pointer;
        }
        .color-preset:hover {
            transform: scale(1.1);
        }
        .color-preset.selected {
            border-color: #000;
            transform: scale(1.1);
        }
        .color-preset[data-color="#ffc107"] {
            border: 2px solid #dee2e6;
        }
    </style>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> Category</h2>
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
                                   value="<?= htmlspecialchars($category['name']) ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a category name.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="slug" 
                                   name="slug" 
                                   value="<?= htmlspecialchars($category['slug']) ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a category slug.
                            </div>
                            <div class="form-text">URL-friendly version of the name (e.g., "health-and-wellness")</div>
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Badge Color</label>
                            <div class="mb-2">
                                <label class="form-label small">Preset Colors:</label>
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-sm color-preset" data-color="#0d6efd" style="background-color: #0d6efd; width: 35px; height: 35px; border-radius: 4px;" title="Primary"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#6c757d" style="background-color: #6c757d; width: 35px; height: 35px; border-radius: 4px;" title="Secondary"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#198754" style="background-color: #198754; width: 35px; height: 35px; border-radius: 4px;" title="Success"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#dc3545" style="background-color: #dc3545; width: 35px; height: 35px; border-radius: 4px;" title="Danger"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#ffc107" style="background-color: #ffc107; width: 35px; height: 35px; border-radius: 4px;" title="Warning"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#0dcaf0" style="background-color: #0dcaf0; width: 35px; height: 35px; border-radius: 4px;" title="Info"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#20c997" style="background-color: #20c997; width: 35px; height: 35px; border-radius: 4px;" title="Teal"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#d63384" style="background-color: #d63384; width: 35px; height: 35px; border-radius: 4px;" title="Pink"></button>
                                    <button type="button" class="btn btn-sm color-preset" data-color="#6f42c1" style="background-color: #6f42c1; width: 35px; height: 35px; border-radius: 4px;" title="Purple"></button>
                                </div>
                            </div>
                            <div class="input-group">
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="color" 
                                       name="color" 
                                       value="<?= htmlspecialchars($category['color'] ?? '#6c757d') ?>"
                                       title="Choose category badge color">
                                <input type="text" 
                                       class="form-control" 
                                       id="colorHex" 
                                       value="<?= htmlspecialchars($category['color'] ?? '#6c757d') ?>" 
                                       readonly>
                            </div>
                            <div class="form-text">Choose a color for the category badge</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                    <?= $category['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <div class="form-text">Inactive categories won't be visible to users</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/categories" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <?= $isEdit ? 'Update' : 'Create' ?> Category
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

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    document.getElementById('slug').value = slug;
});

// Sync color picker with hex input
document.getElementById('color').addEventListener('input', function(e) {
    const color = e.target.value;
    document.getElementById('colorHex').value = color;
    
    // Update selected state of preset buttons
    document.querySelectorAll('.color-preset').forEach(button => {
        if (button.dataset.color.toLowerCase() === color.toLowerCase()) {
            button.classList.add('selected');
        } else {
            button.classList.remove('selected');
        }
    });
});

// Handle color preset selection
document.querySelectorAll('.color-preset').forEach(button => {
    // Set initial selected state
    if (button.dataset.color === document.getElementById('color').value) {
        button.classList.add('selected');
    }
    
    button.addEventListener('click', function() {
        // Remove selected class from all buttons
        document.querySelectorAll('.color-preset').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        // Add selected class to clicked button
        this.classList.add('selected');
        
        const color = this.dataset.color;
        document.getElementById('color').value = color;
        document.getElementById('colorHex').value = color;
    });
});
</script> 