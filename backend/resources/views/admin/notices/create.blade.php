@extends('layouts.admin', ['pageTitle' => 'सूचना सिर्जना'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="megaphone" class="me-2 text-primary"></i>सार्वजनिक सूचना सिर्जना गर्नुहोस्</h6>
                <a href="{{ route('admin.notices.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i data-lucide="arrow-left" class="me-1"></i> पछाडि
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.notices.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">सूचनाको शीर्षक <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="उदा. प्रणाली रखरखाव तथा कार्यालय बन्द रहने सम्बन्धी सूचना">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">विवरण / व्यहोरा <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="6" required placeholder="सूचनाको विस्तृत विवरण यहाँ लेख्नुहोस्..."></textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="published_at" class="form-label">प्रकाशन मिति</label>
                        <input type="date" class="form-control @error('published_at') is-invalid @enderror" id="published_at" name="published_at" value="{{ old('published_at', date('Y-m-d')) }}">
                        @error('published_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label fw-semibold" for="is_active">तुरन्तै प्रकाशित गर्नुहोस्</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.notices.index') }}" class="btn btn-light">रद्द गर्नुहोस्</a>
                        <button type="submit" class="btn btn-primary"><i data-lucide="check" class="me-1"></i> सूचना सुरक्षित गर्नुहोस्</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
