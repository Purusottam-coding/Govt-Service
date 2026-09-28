@extends('layouts.admin', ['pageTitle' => 'QR कोड सम्पादन'])

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
                <h6 class="mb-0 fw-bold"><i data-lucide="edit-2" class="me-2 text-primary"></i>{{ $qrCode->getQrTypeLabel() }} QR कोड सम्पादन</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.qr-codes.update', $qrCode) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-bold">QR कोड प्रकार</label>
                        <input type="text" class="form-control" value="{{ $qrCode->getQrTypeLabel() }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">वर्तमान QR कोड</label>
                        @if($qrCode->hasQrCode())
                            <div class="text-center">
                                <img src="{{ $qrCode->qr_code_url }}" alt="{{ $qrCode->getQrTypeLabel() }} QR Code" 
                                     class="img-fluid" style="max-width: 150px; border: 2px solid #e2e8f0; border-radius: 8px;">
                            </div>
                        @else
                            <div class="text-muted bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px; margin: 0 auto;">
                                <i data-lucide="image-off" class="fs-4"></i>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="qr_code" class="form-label fw-bold">नयाँ QR कोड अपलोड गर्नुहोस् (वैकल्पिक)</label>
                        <input type="file" name="qr_code" id="qr_code" class="form-control @error('qr_code') is-invalid @enderror" 
                               accept="image/*">
                        <div class="form-text">नयाँ QR कोड अपलोड गर्दा पुरानो QR कोड प्रतिस्थापन हुनेछ। PNG, JPG, वा JPEG फाइल (अधिकतम 2MB)</div>
                        @error('qr_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">विवरण (वैकल्पिक)</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="QR कोडको बारेमा थप जानकारी...">{{ old('description', $qrCode->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ $qrCode->status ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                <strong>सक्रिय</strong> - यो QR कोड नागरिकहरूले देख्न सक्छन्
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i data-lucide="save" class="me-1"></i> QR कोड अद्यावधिक गर्नुहोस्
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection