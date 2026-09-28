@extends('layouts.admin', ['pageTitle' => 'विभाग सम्पादन'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="pencil" class="me-2 text-primary"></i>विभाग सम्पादन गर्नुहोस्</h6>
                <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i data-lucide="arrow-left" class="me-1"></i> पछाडि
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">विभागको नाम <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $department->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">विवरण</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">सम्पर्क फोन नम्बर</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $department->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">इमेल ठेगाना</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $department->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $department->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="status">सक्रिय विभाग</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-light">रद्द गर्नुहोस्</a>
                        <button type="submit" class="btn btn-primary"><i data-lucide="check" class="me-1"></i> विभाग अद्यावधिक गर्नुहोस्</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
