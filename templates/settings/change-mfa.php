<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Change MFA Device</h5>
                        <a href="/settings" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Settings
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6>1. Scan QR Code</h6>
                                <p class="text-muted">
                                    Scan this QR code with your authenticator app (e.g., Google Authenticator, Authy).
                                </p>
                                <div class="text-center p-4 bg-light mb-3">
                                    <img src="<?= $qrCode ?>" alt="QR Code" class="img-fluid">
                                </div>
                                <p class="text-muted">
                                    If you can't scan the QR code, you can manually enter this secret key:
                                    <code class="ms-2"><?= chunk_split($secret, 4, ' ') ?></code>
                                </p>
                            </div>

                            <div>
                                <h6>2. Verify New Device</h6>
                                <p class="text-muted mb-3">
                                    Enter the verification code from your authenticator app to confirm the setup.
                                </p>
                                <form action="/settings/verify-mfa" method="POST">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">Verification Code</label>
                                        <input type="text" class="form-control" id="code" name="code" 
                                               required maxlength="6" minlength="6" pattern="[0-9]*"
                                               style="max-width: 200px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        Verify and Save
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Important Notes:</h6>
                                <ul class="mb-0">
                                    <li>Your current MFA device will continue to work until you complete the setup of the new device.</li>
                                    <li>Once you verify the new device, your old device will stop working immediately.</li>
                                    <li>Make sure you have access to the new device before completing the verification.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 