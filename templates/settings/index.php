<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Settings</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Change Password Section -->
                    <div class="mb-5">
                        <h6 class="mb-3">Change Password</h6>
                        <form action="/settings/change-password" method="POST">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Password</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Change MFA Device Section -->
                    <div>
                        <h6 class="mb-3">Two-Factor Authentication</h6>
                        <p class="text-muted mb-3">
                            You can change your MFA device by setting up a new one. Your current MFA device will be replaced
                            once you complete the setup of the new device.
                        </p>
                        <a href="/settings/change-mfa" class="btn btn-primary">
                            Change MFA Device
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 