<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-sm" style="width: 400px;">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Two-Factor Authentication</h3>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <p class="text-center mb-4">Please enter the verification code sent to your device.</p>

            <form action="/verify-mfa" method="POST">
                <div class="mb-3">
                    <label for="mfa_code" class="form-label">Verification Code</label>
                    <input type="text" class="form-control form-control-lg text-center" id="mfa_code" name="mfa_code" 
                           maxlength="6" pattern="\d{6}" title="Please enter a 6-digit code" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify</button>
            </form>
        </div>
    </div>
</div> 