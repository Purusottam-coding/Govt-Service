<section>
    <header class="mb-4">
        <h5 class="fw-bold text-dark">
            व्यक्तिगत विवरण (Profile Information)
        </h5>
        <p class="text-muted small">
            तपाईंको खाताको प्रोफाइल विवरण र इमेल ठेगाना अद्यावधिक गर्नुहोस्।
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-3">
        @csrf
        @method('patch')

        <!-- Profile Photo Preview & Upload -->
        <div class="mb-4 d-flex align-items-center gap-3">
            <div class="position-relative">
                @if($user->profile_photo)
                    <img id="avatarPreview" src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->name }}" class="rounded-circle object-fit-cover border border-2 border-primary" style="width: 80px; height: 80px;">
                @else
                    <div id="avatarPreviewPlaceholder" class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-3 border border-2 border-primary" style="width: 80px; height: 80px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-grow-1">
                <label for="profile_photo" class="form-label fw-semibold mb-1">प्रोफाइल तस्वीर (Profile Photo)</label>
                <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="form-control form-control-sm @error('profile_photo') is-invalid @enderror" onchange="previewImage(this)">
                <small class="text-muted extra-small d-block mt-1">अनुमति दिइएका फाईल: JPG, PNG, WEBP (अधिकतम 2MB)</small>
                @error('profile_photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">पूरा नाम <span class="text-danger">*</span></label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">इमेल ठेगाना <span class="text-danger">*</span></label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 alert alert-warning py-2 small">
                    तपाईंको इमेल ठेगाना प्रमाणित गरिएको छैन।
                    <button form="send-verification" class="btn btn-link p-0 text-decoration-underline small">
                        प्रमाणीकरण इमेल पुनः पठाउन यहाँ क्लिक गर्नुहोस्।
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-2 text-success fw-bold">
                            नयाँ प्रमाणीकरण लिङ्क तपाईंको इमेल ठेगानामा पठाइएको छ।
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="phone" class="form-label">फोन नम्बर</label>
                <input id="phone" name="phone" type="text" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="उदा: 98XXXXXXXX" />
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="address" class="form-label">ठेगाना</label>
                <input id="address" name="address" type="text" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $user->address) }}" placeholder="उदा: काठमाडौँ, नेपाल" />
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary"><i data-lucide="save" class="me-1"></i> सुरक्षित गर्नुहोस्</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small fw-semibold"><i data-lucide="check-circle-2" class="me-1"></i>विवरण सुरक्षित गरियो।</span>
            @endif
        </div>
    </form>
</section>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPreviewPlaceholder');
                if (img) {
                    img.src = e.target.result;
                } else if (placeholder) {
                    const newImg = document.createElement('img');
                    newImg.id = 'avatarPreview';
                    newImg.src = e.target.result;
                    newImg.className = 'rounded-circle object-fit-cover border border-2 border-primary';
                    newImg.style.width = '80px';
                    newImg.style.height = '80px';
                    placeholder.parentNode.replaceChild(newImg, placeholder);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
