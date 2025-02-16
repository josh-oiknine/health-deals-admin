<div class="container-fluid">
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header">
          <h2 class="mb-0"><?= $isEdit ? 'Edit' : 'Add' ?> User</h2>
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
              <label for="email" class="form-label">Email *</label>
              <input type="email" 
                   class="form-control" 
                   id="email" 
                   name="email" 
                   value="<?= htmlspecialchars($user->getEmail()) ?>"
                   required>
              <div class="invalid-feedback">
                Please provide a valid email address.
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">
                <?= $isEdit ? 'New Password (leave blank to keep current)' : 'Password *' ?>
              </label>
              <input type="password" 
                   class="form-control" 
                   id="password" 
                   name="password"
                   <?= $isEdit ? '' : 'required' ?>>
              <div class="invalid-feedback">
                Please provide a password.
              </div>
            </div>

            <div class="mb-3">
              <label for="first_name" class="form-label">First Name *</label>
              <input type="text" 
                   class="form-control" 
                   id="first_name" 
                   name="first_name" 
                   value="<?= htmlspecialchars($user->getFirstName()) ?>"
                   required>
              <div class="invalid-feedback">
                Please provide a first name.
              </div>
            </div>

            <div class="mb-3">
              <label for="last_name" class="form-label">Last Name *</label>
              <input type="text" 
                   class="form-control" 
                   id="last_name" 
                   name="last_name" 
                   value="<?= htmlspecialchars($user->getLastName()) ?>"
                   required>
              <div class="invalid-feedback">
                Please provide a last name.
              </div>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input type="checkbox" 
                     class="form-check-input" 
                     id="is_active" 
                     name="is_active"
                     <?= $user->isActive() ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">Active</label>
              </div>
            </div>

            <div class="d-flex justify-content-between">
              <a href="/users" class="btn btn-outline-secondary">Cancel</a>
              <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Update' : 'Create' ?> User
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
</script> 