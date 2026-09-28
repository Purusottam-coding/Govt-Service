@extends('layouts.admin', ['pageTitle' => 'नयाँ QR कोड थप्नुहोस्'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.qr-codes.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> QR कोड सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="qr-code" class="me-2 text-primary"></i>नयाँ भुक्तानी QR कोड थप्नुहोस्</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.qr-codes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="qr_type" class="form-label fw-bold">QR कोड प्रकार <span class="text-danger">*</span></label>
                        <select name="qr_type" id="qr_type" class="form-select @error('qr_type') is-invalid @enderror" required>
                            <option value="">छान्नुहोस्</option>
                            <option value="esewa">eSewa</option>
                            <option value="khalti">Khalti</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                        @error('qr_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="qr_code" class="form-label fw-bold">QR कोड अपलोड गर्नुहोस् <span class="text-danger">*</span></label>
                        <input type="file" name="qr_code" id="qr_code" class="form-control @error('qr_code') is-invalid @enderror" 
                               accept="image/*" required>
                        <div class="form-text">PNG, JPG, वा JPEG फाइल (अधिकतम 2MB)</div>
                        @error('qr_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">विवरण (वैकल्पिक)</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="QR कोडको बारेमा थप जानकारी...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="status" value="1" checked>
                            <label class="form-check-label" for="status">
                                <strong>सक्रिय</strong> - यो QR कोड नागरिकहरूले देख्न सक्छन्
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i data-lucide="save" class="me-1"></i> QR कोड सेभ गर्नुहोस्
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection