<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Set Up Two-Factor Authentication</h3>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="text-center mb-4">
                <p>Scan this QR code with your authenticator app:</p>
                <img src="<?= $qrCode ?>" alt="QR Code" class="img-fluid mb-3">
                <p class="small text-muted">Or manually enter this code:<br>
                <code><?= chunk_split($secret, 4, ' ') ?></code></p>
            </div>

            <form action="/setup-2fa" method="POST">
                <div class="mb-3">
                    <label for="code" class="form-label">Verification Code</label>
                    <input type="text" class="form-control form-control-lg text-center" 
                           id="code" name="code" maxlength="6" pattern="\d{6}" 
                           title="Please enter a 6-digit code" required>
                    <div class="form-text">
                        Enter the 6-digit code from your authenticator app
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify and Enable 2FA</button>
            </form>
        </div>
    </div>
</div> 