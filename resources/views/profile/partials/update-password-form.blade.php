<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">
            <i class="bi bi-lock"></i> Current Password
        </label>
        <input type="password" class="form-control" id="update_password_current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">
            <i class="bi bi-key"></i> New Password
        </label>
        <input type="password" class="form-control" id="update_password_password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label">
            <i class="bi bi-key-fill"></i> Confirm Password
        </label>
        <input type="password" class="form-control" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password">
        @error('password_confirmation', 'updatePassword')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-purple">
            <i class="bi bi-check-circle"></i> Update Password
        </button>

        @if (session('status') === 'password-updated')
            <p class="mb-0 small text-success">
                <i class="bi bi-check-circle-fill"></i> Password updated successfully!
            </p>
        @endif
    </div>
</form>
