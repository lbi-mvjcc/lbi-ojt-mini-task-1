<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <!-- Profile Picture Upload -->
    <div class="mb-4">
        <label class="form-label">
            <i class="bi bi-camera"></i> Profile Picture
        </label>
        <div class="d-flex align-items-center gap-3">
            <div class="profile-picture-preview" style="position: relative;">
                @if($user->profile_picture)
                    <img id="profilePicturePreview" src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" 
                         style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-purple);">
                @else
                    <div id="profilePicturePreview" style="width: 100px; height: 100px; border-radius: 50%; background: var(--purple-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; border: 3px solid var(--primary-purple);">
                        <i class="bi bi-person-circle"></i>
                    </div>
                @endif
            </div>
            <div class="flex-grow-1">
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewProfilePicture(event)">
                <div class="text-muted small mt-1" style="background: transparent !important; padding: 0 !important;">
                    <i class="bi bi-info-circle"></i> Accepted formats: JPG, PNG, GIF (Max: 2MB)
                </div>
                @error('profile_picture')
                    <div class="text-danger mt-1 small">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-person"></i> Name
        </label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email
        </label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="text-danger mt-1 small">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="small text-muted">
                    Your email address is unverified.
                    <button form="send-verification" class="btn btn-link p-0 text-decoration-underline small">
                        Click here to re-send the verification email.
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 small text-success">
                        A new verification link has been sent to your email address.
                    </p>
                @endif
            </div>
        @endif
    </div>

    <div class="d-flex align-items-center gap-3">
        <button type="submit" class="btn btn-purple">
            <i class="bi bi-check-circle"></i> Save Changes
        </button>

        @if (session('status') === 'profile-updated')
            <p class="mb-0 small text-success">
                <i class="bi bi-check-circle-fill"></i> Saved successfully!
            </p>
        @endif
    </div>
</form>

<script>
function previewProfilePicture(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profilePicturePreview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Profile Picture" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-purple);">`;
        }
        reader.readAsDataURL(file);
    }
}
</script>
