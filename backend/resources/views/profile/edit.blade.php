@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.citizen', ['pageTitle' => 'प्रोफाइल सम्पादन'])

@section('content')
<div class="container py-2">
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i data-lucide="user" class="me-2 text-primary"></i>व्यक्तिगत विवरण अद्यावधिक</h6>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i data-lucide="shield" class="me-2 text-primary"></i>पासवर्ड परिवर्तन गर्नुहोस्</h6>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="mb-0 fw-bold"><i data-lucide="alert-triangle" class="me-2"></i>खाता हटाउनुहोस् (Delete Account)</h6>
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
