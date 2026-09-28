@extends('layouts.admin', ['pageTitle' => 'निवेदन समीक्षा'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> निवेदन सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row g-4">
    <!-- Application Details & Documents -->
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="file-earmark-person" class="me-2 text-primary"></i>निवेदन नं. #{{ $application->application_number }}</h6>
                <span class="badge-status {{ $application->getStatusBadgeClass() }}">{{ $application->getStatusLabel() }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <span class="text-muted small d-block">अनुरोध गरिएको सेवा</span>
                        <span class="fw-bold text-dark">{{ $application->service->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted small d-block">मन्त्रालय / विभाग</span>
                        <span class="fw-semibold">{{ $application->service->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted small d-block">पेश गरेको मिति</span>
                        <span class="fw-semibold">{{ $application->submitted_at ? $application->submitted_at->format('M d, Y h:i A') : $application->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-muted small d-block">प्रशोधन मिति</span>
                        <span class="fw-semibold">{{ $application->processed_at ? $application->processed_at->format('M d, Y') : 'अझै प्रशोधन भएको छैन' }}</span>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold text-dark mb-3"><i data-lucide="user" class="me-2 text-primary"></i>निवेदकको विवरण</h6>
                <div class="row g-3 mb-4 bg-light p-3 rounded">
                    <div class="col-md-6">
                        <span class="text-muted small d-block">पूरा नाम</span>
                        <span class="fw-semibold text-dark">{{ $application->applicant_name }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">इमेल ठेगाना</span>
                        <span class="fw-semibold text-dark">{{ $application->applicant_email }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">फोन नम्बर</span>
                        <span class="fw-semibold text-dark">{{ $application->applicant_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted small d-block">ठेगाना</span>
                        <span class="fw-semibold text-dark">{{ $application->applicant_address ?? 'N/A' }}</span>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3"><i data-lucide="file-check" class="me-2 text-primary"></i>अपलोड गरिएका कागजातहरू</h6>
                @if($application->documents->count() > 0)
                    <div class="row g-2 mb-4">
                        @foreach($application->documents as $doc)
                            <div class="col-12 col-md-6">
                                <div class="p-3 border rounded d-flex justify-content-between align-items-center bg-white">
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="file-text" class="fs-4 text-danger"></i>
                                        <div>
                                            <div class="fw-semibold small">{{ $doc->document_name }}</div>
                                            <span class="text-muted extra-small">अपलोड गरिएको</span>
                                        </div>
                                    </div>
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i data-lucide="download"></i> हेर्नुहोस्
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded text-muted small mb-4">यस निवेदनको लागि कुनै पनि कागजात अपलोड गरिएको छैन।</div>
                @endif

                @if($application->hasApprovedDocument())
                    <div class="card border-success shadow-sm mb-4">
                        <div class="card-header bg-success text-white py-2.5 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold small"><i data-lucide="award" class="me-1"></i> जारी गरिएको आधिकारिक प्रमाणपत्र / स्वीकृत कागजात</h6>
                            <span class="badge bg-white text-success fw-bold font-monospace fs-7">
                                ID: {{ $application->certificate_number }}
                            </span>
                        </div>
                        <div class="card-body p-3 bg-light">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $application->approved_document_name }}</div>
                                    <div class="small text-muted">
                                        <i data-lucide="calendar" style="width: 12px; height: 12px;"></i> जारी मिति: {{ $application->issued_at ? $application->issued_at->format('M d, Y h:i A') : 'N/A' }} 
                                        &bull; फाइल प्रकार: <span class="text-uppercase fw-semibold">{{ $application->approved_document_type }}</span>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ $application->getApprovedDocumentUrl() }}" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i data-lucide="eye" class="me-1"></i> हेर्नुहोस्
                                    </a>
                                    <a href="{{ $application->getApprovedDocumentUrl() }}" download class="btn btn-sm btn-success">
                                        <i data-lucide="download" class="me-1"></i> डाउनलोड
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($application->admin_remarks)
                    <div class="alert alert-info mb-0">
                        <h6 class="fw-bold mb-1"><i data-lucide="chat-left-text" class="me-1"></i> प्रशासकीय टिप्पणी:</h6>
                        <p class="mb-0 small">{{ $application->admin_remarks }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Status Update & Payment Info Sidebar -->
    <div class="col-12 col-lg-4">
        <!-- Update Status Form -->
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="edit" class="me-2 text-primary"></i>निवेदन स्थिति तथा कागजात</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.applications.status', $application) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">स्थिति <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>पेश गरिएको (पेन्डिङ)</option>
                            <option value="under_review" {{ $application->status == 'under_review' ? 'selected' : '' }}>छानबिनमा</option>
                            <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>स्वीकृत (Approved)</option>
                            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>अस्वीकृत</option>
                            <option value="completed" {{ $application->status == 'completed' ? 'selected' : '' }}>सम्पन्न</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Approved Document Upload Box -->
                    <div class="p-3 mb-3 rounded-3 border bg-light" id="approvedDocSection">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1">
                            <span><i data-lucide="file-check" class="me-1 text-success"></i> स्वीकृत कागजात / प्रमाणपत्र</span>
                            <span class="badge bg-secondary-subtle text-secondary small">ऐच्छिक</span>
                        </label>
                        <p class="text-muted extra-small mb-2" style="font-size: 0.75rem;">
                            नागरिकलाई प्रदान गरिने स्वीकृत पत्र, सिफारिस वा प्रमाणपत्र (PDF/Image)
                        </p>

                        @if($application->hasApprovedDocument())
                            <div class="alert alert-success py-2 px-2.5 small mb-2 d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2">
                                    <i data-lucide="check-circle" class="me-1 text-success" style="width: 14px; height: 14px;"></i>
                                    <strong>{{ $application->approved_document_name }}</strong>
                                </div>
                                <a href="{{ $application->getApprovedDocumentUrl() }}" target="_blank" class="btn btn-xs btn-outline-success">
                                    <i data-lucide="external-link" style="width: 12px; height: 12px;"></i>
                                </a>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="remove_approved_document" value="1" id="removeDocCheck">
                                <label class="form-check-label text-danger small" for="removeDocCheck">
                                    हालको कागजात हटाउनुहोस्
                                </label>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label extra-small text-muted mb-1">कागजात फाइल (नयाँ वा प्रतिस्थापन):</label>
                            <input type="file" 
                                   name="approved_document" 
                                   id="approved_document" 
                                   class="form-control form-control-sm @error('approved_document') is-invalid @enderror"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            @error('approved_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label extra-small text-muted mb-1">कागजातको शीर्षक / नाम:</label>
                            <input type="text" 
                                   name="approved_document_name" 
                                   class="form-control form-control-sm" 
                                   value="{{ old('approved_document_name', $application->approved_document_name) }}" 
                                   placeholder="उदा. नागरिकता सिफारिस पत्र">
                        </div>

                        <div class="mb-1">
                            <label class="form-label extra-small text-muted mb-1 d-flex justify-content-between">
                                <span>प्रमाणपत्र / यूनिक ID (Unique Verification ID):</span>
                                <span class="text-primary font-monospace">उदा. ABC123</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       name="certificate_number" 
                                       id="certificate_number"
                                       class="form-control form-control-sm font-monospace text-uppercase fw-bold" 
                                       value="{{ old('certificate_number', $application->certificate_number) }}" 
                                       placeholder="खाली छोडेमा स्वतः ABC123 बन्नेछ">
                                <button class="btn btn-outline-secondary" type="button" onclick="generateNewCertId()" title="नयाँ ID जेनेरेट गर्नुहोस्">
                                    <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="admin_remarks" class="form-label fw-semibold">प्रशासकीय टिप्पणी / निर्देशन</label>
                        <textarea name="admin_remarks" id="admin_remarks" rows="3" class="form-control @error('admin_remarks') is-invalid @enderror" placeholder="स्थिति परिवर्तनको कारण वा निर्देशन लेख्नुहोस् — निवेदकले देख्न सक्नुहुनेछ">{{ old('admin_remarks', $application->admin_remarks) }}</textarea>
                        @error('admin_remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold"><i data-lucide="save" class="me-1"></i> स्थिति तथा कागजात सुरक्षित गर्नुहोस्</button>
                </form>
            </div>
        </div>

        <!-- Payment Info Card -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="credit-card" class="me-2 text-primary"></i>भुक्तानी विवरण</h6>
            </div>
            <div class="card-body">
                @if($application->payment)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">चुक्ता रकम:</span>
                        <span class="fw-bold text-success">रु. {{ number_format($application->payment->amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">भुक्तानी स्थिति:</span>
                        @if($application->payment->status === 'pending')
                            <span class="badge bg-primary">प्रमाणीकरणमा</span>
                        @elseif($application->payment->status === 'completed')
                            <span class="badge bg-success">चुक्ता भएको</span>
                        @else
                            <span class="badge bg-danger">असफल</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">भुक्तानी माध्यम:</span>
                        <span class="fw-semibold text-uppercase">{{ $application->payment->payment_method }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">कारोबार नम्बर:</span>
                        <span class="fw-mono small">{{ $application->payment->transaction_id ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">भुक्तानी मिति:</span>
                        <span class="small">{{ $application->payment->paid_at ? $application->payment->paid_at->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    @if($application->payment->payment_statement)
                        <div class="mt-3">
                            <a href="{{ Storage::url($application->payment->payment_statement) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i data-lucide="image" class="me-1"></i> भुक्तानी स्टेटमेन्ट हेर्नुहोस्
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i data-lucide="alert-circle" class="text-warning fs-3 mb-2 d-block"></i>
                        <span class="text-muted small">भुक्तानीको कुनै रेकर्ड भेटिएन।</span>
                        @if(($application->service->fee ?? 0) > 0)
                            <div class="mt-2 fw-bold text-dark">बाँकी दस्तुर: रु. {{ number_format($application->service->fee, 2) }}</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateNewCertId() {
    const letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    const digits = '123456789';
    let code = '';
    for (let i = 0; i < 3; i++) {
        code += letters.charAt(Math.floor(Math.random() * letters.length));
    }
    for (let i = 0; i < 3; i++) {
        code += digits.charAt(Math.floor(Math.random() * digits.length));
    }
    const certInput = document.getElementById('certificate_number');
    if (certInput) {
        certInput.value = code;
    }
}
</script>
@endpush
