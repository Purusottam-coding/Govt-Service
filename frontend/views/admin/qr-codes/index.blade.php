@extends('layouts.admin', ['pageTitle' => 'QR कोड व्यवस्थापन'])

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold"><i data-lucide="qr-code" class="me-2 text-primary"></i>भुक्तानी QR कोड व्यवस्थापन</h5>
    <a href="{{ route('admin.qr-codes.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> नयाँ QR कोड थप्नुहोस्
    </a>
</div>

<div class="row g-4">
    @forelse($qrCodes as $qrCode)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ $qrCode->getQrTypeLabel() }}</h6>
                    <div class="d-flex gap-2">
                        @if($qrCode->status)
                            <span class="badge bg-success">सक्रिय</span>
                        @else
                            <span class="badge bg-secondary">निष्क्रिय</span>
                        @endif
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if($qrCode->hasQrCode())
                            <img src="{{ $qrCode->qr_code_url }}" alt="{{ $qrCode->getQrTypeLabel() }} QR Code" 
                                 class="img-fluid" style="max-width: 150px; border: 2px solid #e2e8f0; border-radius: 8px;">
                        @else
                            <div class="text-muted bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px; margin: 0 auto;">
                                <i data-lucide="image-off" class="fs-4"></i>
                            </div>
                        @endif
                    </div>
                    @if($qrCode->description)
                        <p class="small text-muted mb-0">{{ $qrCode->description }}</p>
                    @endif
                </div>
                <div class="card-footer bg-white py-3 d-flex justify-content-between">
                    <a href="{{ route('admin.qr-codes.edit', $qrCode) }}" class="btn btn-sm btn-outline-primary">
                        <i data-lucide="edit-2" class="me-1"></i> सम्पादन
                    </a>
                    <form action="{{ route('admin.qr-codes.toggle-status', $qrCode) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $qrCode->status ? 'btn-outline-warning' : 'btn-outline-success' }}">
                            <i data-lucide="{{ $qrCode->status ? 'toggle-left' : 'toggle-right' }}" class="me-1"></i>
                            {{ $qrCode->status ? 'निष्क्रिय' : 'सक्रिय' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.qr-codes.destroy', $qrCode) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('के तपाईं निश्चित हुनुहुन्छ कि तपाईं यो QR कोड मेटाउन चाहनुहुन्छ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i data-lucide="trash-2" class="me-1"></i> मेटाउनुहोस्
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i data-lucide="qr-code" class="fs-3 mb-2 d-block mx-auto"></i>
                <h6 class="fw-bold mb-1">कुनै QR कोड भेटिएन</h6>
                <p class="small mb-0">भुक्तानी प्रक्रियाको लागि QR कोड थप्नुहोस्</p>
            </div>
        </div>
    @endforelse
</div>
@endsection