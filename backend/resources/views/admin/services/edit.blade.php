@extends('layouts.admin', ['pageTitle' => 'सेवा सम्पादन'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="pencil" class="me-2 text-primary"></i>सरकारी सेवा सम्पादन गर्नुहोस्</h6>
                <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i data-lucide="arrow-left" class="me-1"></i> पछाडि
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.services.update', $service) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="department_id" class="form-label">मन्त्रालय / विभाग <span class="text-danger">*</span></label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">विभाग चयन गर्नुहोस्</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $service->department_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">सेवाको नाम <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $service->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">सेवाको विवरण</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="fee" class="form-label">सेवा दस्तुर (NPR / रु.) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee', $service->fee) }}" required>
                            @error('fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="processing_days" class="form-label">अनुमानित प्रशोधन समय (दिन) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('processing_days') is-invalid @enderror" id="processing_days" name="processing_days" value="{{ old('processing_days', $service->processing_days) }}" required>
                            @error('processing_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="required_documents" class="form-label">आवश्यक कागजातहरू (कमवाद्वारा विभाजित)</label>
                        <input type="text" class="form-control @error('required_documents') is-invalid @enderror" id="required_documents" name="required_documents" value="{{ old('required_documents', is_array($service->required_documents) ? implode(', ', $service->required_documents) : '') }}">
                        <small class="text-muted">नागरिकहरूले आवेदन दिँदा यी कागजातहरू अपलोड गर्नुपर्नेछ।</small>
                        @error('required_documents')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $service->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="status">सक्रिय तथा नागरिकहरूको लागि उपलब्ध</label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.services.index') }}" class="btn btn-light">रद्द गर्नुहोस्</a>
                        <button type="submit" class="btn btn-primary"><i data-lucide="check" class="me-1"></i> सेवा अद्यावधिक गर्नुहोस्</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
