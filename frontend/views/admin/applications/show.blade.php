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
                    <div class="row g-3 mb-4">
                        @foreach($application->documents as $doc)
                            <div class="col-12 col-md-6">
                                <div class="p-3 border rounded bg-white h-100 d-flex flex-column justify-content-between {{ $doc->isReplacementNeeded() ? 'border-danger bg-danger-subtle bg-opacity-10' : '' }}">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <i data-lucide="file-text" class="text-danger"></i>
                                                <span class="fw-bold small text-dark">{{ $doc->document_name }}</span>
                                            </div>
                                            @if($doc->isReplacementNeeded())
                                                <span class="badge bg-danger text-white extra-small"><i data-lucide="alert-triangle" style="width: 10px; height: 10px;" class="me-1"></i>प्रतिस्थापन माग गरिएको</span>
                                            @elseif($doc->replaced_at)
                                                <span class="badge bg-info-subtle text-info extra-small"><i data-lucide="refresh-cw" style="width: 10px; height: 10px;" class="me-1"></i>पुनः अपलोड भएको</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary extra-small">पेस गरिएको</span>
                                            @endif
                                        </div>

                                        @if($doc->admin_feedback)
                                            <div class="alert alert-danger py-1.5 px-2 mb-2 extra-small" style="font-size: 0.76rem;">
                                                <strong>कैफियत:</strong> {{ $doc->admin_feedback }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 pt-2 border-top mt-2">
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i data-lucide="eye" style="width: 13px; height: 13px;"></i> हेर्नुहोस्
                                        </a>

                                        @if(!in_array($application->status, ['approved', 'completed']))
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#replaceDocModal"
                                                    data-doc-name="{{ $doc->document_name }}"
                                                    data-action-url="{{ route('admin.applications.documents.request-replacement', [$application, $doc]) }}"
                                                    data-feedback="{{ $doc->admin_feedback ?? '' }}">
                                                <i data-lucide="rotate-ccw" style="width: 13px; height: 13px;"></i> प्रतिस्थापन माग
                                            </button>
                                        @endif
                                    </div>
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

        <!-- Interactive AJAX Remarks Card (As in Workflow Sketch) -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary p-1.5 rounded-circle">
                        <i data-lucide="message-square" style="width: 14px; height: 14px;"></i>
                    </span>
                    <h6 class="mb-0 fw-bold text-dark">प्रशासकीय संवाद तथा टिप्पणी (Live Remarks)</h6>
                </div>
                <button type="button" class="btn btn-xs btn-outline-secondary" onclick="loadRemarks()" title="टिप्पणीहरू रिफ्रेस गर्नुहोस्">
                    <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i> रिफ्रेस
                </button>
            </div>
            <div class="card-body p-3 bg-light bg-opacity-50">
                <!-- Remarks Thread -->
                <div id="remarksThread" class="d-flex flex-column gap-2 mb-3 p-2.5 overflow-auto bg-white rounded border" style="max-height: 280px; min-height: 100px;">
                    <div class="text-center text-muted small py-4" id="remarksLoading">
                        <div class="spinner-border spinner-border-sm text-primary me-1" role="status"></div> टिप्पणीहरू लोड हुँदैछ...
                    </div>
                </div>

                <!-- Add New Remark Form (AJAX) -->
                <form id="addRemarkForm" onsubmit="submitRemark(event)">
                    @csrf
                    <div class="input-group">
                        <textarea name="message" id="remarkMessageInput" rows="2" class="form-control form-control-sm" placeholder="निवेदक (नागरिक) का लागि यहाँ टिप्पणी वा निर्देशन लेख्नुहोस्..." required maxlength="1000"></textarea>
                        <button type="submit" id="remarkSubmitBtn" class="btn btn-primary px-3 d-flex align-items-center gap-1 fw-bold">
                            <i data-lucide="send" style="width: 14px; height: 14px;"></i> <span>पठाउनुहोस्</span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted extra-small" style="font-size: 0.72rem;">* यो संवाद निवेदकले आफ्नो ड्यासबोर्डमा तत्काल देख्नेछन्।</small>
                        <small class="text-muted extra-small" id="remarkCharCount" style="font-size: 0.72rem;">० / १०००</small>
                    </div>
                </form>
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

                    <!-- Approved Document Upload Box (Shown only when status is 'approved') -->
                    <div class="p-3 mb-3 rounded-3 border bg-light" id="approvedDocSection" style="{{ old('status', $application->status) === 'approved' ? '' : 'display: none;' }}">
                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1">
                            <span><i data-lucide="file-check" class="me-1 text-success"></i> स्वीकृत कागजात / प्रमाणपत्र</span>
                            <span class="badge bg-danger-subtle text-danger small">अनिवार्य *</span>
                        </label>
                        <p class="text-muted extra-small mb-2" style="font-size: 0.75rem;">
                            नागरिकलाई प्रदान गरिने स्वीकृत पत्र, सिफारिस वा प्रमाणपत्र (PDF/Image) अनिवार्य रूपमा अपलोड गर्नुहोस्।
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
                                    हालको कागजात हटाउनुहोस् (नयाँ अनिवार्य अपलोड गर्नुपर्नेछ)
                                </label>
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label extra-small fw-semibold text-dark mb-1">
                                कागजात फाइल <span class="text-danger">*</span> (नयाँ वा प्रतिस्थापन):
                            </label>
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
                            <label class="form-label extra-small fw-semibold text-dark mb-1">
                                कागजातको शीर्षक / नाम <span class="text-danger">*</span>:
                            </label>
                            <input type="text" 
                                   name="approved_document_name" 
                                   id="approved_document_name"
                                   class="form-control form-control-sm @error('approved_document_name') is-invalid @enderror" 
                                   value="{{ old('approved_document_name', $application->approved_document_name ?: ($application->service->name ? $application->service->name . ' — प्रमाणपत्र' : '')) }}" 
                                   placeholder="उदा. नागरिकता सिफारिस पत्र">
                            @error('approved_document_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-1">
                            <label class="form-label extra-small fw-semibold text-dark mb-1 d-flex justify-content-between align-items-center">
                                <span>प्रमाणपत्र / यूनिक ID (Unique Verification ID):</span>
                                <span class="badge bg-primary-subtle text-primary small"><i data-lucide="sparkles" style="width: 11px; height: 11px;" class="me-1"></i>स्वचालित Unique ID</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       name="certificate_number" 
                                       id="certificate_number"
                                       class="form-control form-control-sm font-monospace text-uppercase fw-bold bg-light" 
                                       value="{{ old('certificate_number', $application->certificate_number ?: $suggestedCertificateId) }}" 
                                       readonly
                                       placeholder="स्वचालित Unique ID">
                                <button class="btn btn-outline-secondary" type="button" onclick="generateNewCertId()" title="नयाँ ID पुनः जेनेरेट गर्नुहोस्">
                                    <i data-lucide="refresh-cw" style="width: 12px; height: 12px;"></i>
                                </button>
                            </div>
                            <small class="text-muted extra-small" style="font-size: 0.72rem;">प्रणालीद्वारा स्वतः ६-अङ्कीय युनिक कोड (उदा. ABC123) तयार गरिएको छ।</small>
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

@push('modals')
<!-- Single Reusable Modal for Document Replacement Request (Pushed directly to <body>) -->
<div class="modal fade" id="replaceDocModal" tabindex="-1" aria-labelledby="replaceDocModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <form id="replaceDocForm" action="" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white py-2.5">
                    <h6 class="modal-title fw-bold" id="replaceDocModalLabel">
                        <i data-lucide="alert-circle" class="me-1"></i> कागजात प्रतिस्थापन अनुरोध
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3 p-2.5 rounded bg-light border">
                        <span class="text-muted extra-small d-block">कागजातको शीर्षक:</span>
                        <strong class="text-dark fs-6" id="replaceModalDocName">--</strong>
                    </div>
                    <div class="mb-2">
                        <label for="replaceModalFeedback" class="form-label fw-semibold small text-dark">
                            प्रतिस्थापन गर्नुपर्ने कारण / निर्देशन <span class="text-danger">*</span>
                        </label>
                        <textarea name="admin_feedback" id="replaceModalFeedback" rows="3" class="form-control form-control-sm" required placeholder="उदा. नागरिकताको पछाडिको भाग स्पष्ट देखिएन, कृपया पुनः स्पष्ट फोटो खिचेर अपलोड गर्नुहोस्।"></textarea>
                        <small class="text-muted extra-small">यो निर्देशन निवेदक (नागरिक) ले आफ्नो ड्यासबोर्डमा देख्नेछन्।</small>
                    </div>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">रद्द गर्नुहोस्</button>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold">
                        <i data-lucide="send" style="width: 13px; height: 13px;"></i> अनुरोध पठाउनुहोस्
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

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

document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.getElementById('status');
    const docSection = document.getElementById('approvedDocSection');
    const certInput = document.getElementById('certificate_number');
    const fileInput = document.getElementById('approved_document');
    const nameInput = document.getElementById('approved_document_name');
    const removeDocCheck = document.getElementById('removeDocCheck');
    const hasExistingDoc = {{ $application->hasApprovedDocument() ? 'true' : 'false' }};

    function updateFieldRequirements() {
        if (!statusSelect) return;
        const isApproved = statusSelect.value === 'approved';

        if (nameInput) {
            nameInput.required = isApproved;
        }

        if (fileInput) {
            // File is required when status is approved and either no document exists or existing one is being removed
            const needsFile = isApproved && (!hasExistingDoc || (removeDocCheck && removeDocCheck.checked));
            fileInput.required = needsFile;
        }
    }

    function toggleApprovedDocSection() {
        if (!statusSelect || !docSection) return;

        if (statusSelect.value === 'approved') {
            docSection.style.display = 'block';
            // Auto-populate unique certificate ID if currently empty
            if (certInput && !certInput.value.trim()) {
                generateNewCertId();
            }
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        } else {
            docSection.style.display = 'none';
        }

        updateFieldRequirements();
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleApprovedDocSection);
        toggleApprovedDocSection();
    }

    if (removeDocCheck) {
        removeDocCheck.addEventListener('change', updateFieldRequirements);
    }

    // Dynamic populate for single document replacement modal
    const replaceModal = document.getElementById('replaceDocModal');
    if (replaceModal) {
        replaceModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const docName = button.getAttribute('data-doc-name') || '';
            const actionUrl = button.getAttribute('data-action-url') || '';
            const feedback = button.getAttribute('data-feedback') || '';

            const form = document.getElementById('replaceDocForm');
            const nameEl = document.getElementById('replaceModalDocName');
            const feedbackEl = document.getElementById('replaceModalFeedback');

            if (form) form.action = actionUrl;
            if (nameEl) nameEl.textContent = docName;
            if (feedbackEl) feedbackEl.value = feedback;

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    }

    // Initialize AJAX Remarks
    loadRemarks();

    // Char count listener
    const remarkInput = document.getElementById('remarkMessageInput');
    const charCountEl = document.getElementById('remarkCharCount');
    if (remarkInput && charCountEl) {
        remarkInput.addEventListener('input', function() {
            charCountEl.textContent = this.value.length + ' / 1000';
        });
    }
});

/* ---------- AJAX Application Remarks Logic ---------- */
const remarksUrl = "{{ route('applications.remarks.index', $application) }}";
const remarksStoreUrl = "{{ route('applications.remarks.store', $application) }}";
const csrfToken = "{{ csrf_token() }}";

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderRemarkItem(remark) {
    const isAdmin = remark.sender_role === 'admin';
    const isMe = remark.is_me;
    const badgeHtml = isAdmin 
        ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;"><i data-lucide="shield" style="width:10px;height:10px;" class="me-1"></i>प्रशासन</span>`
        : `<span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;"><i data-lucide="user" style="width:10px;height:10px;" class="me-1"></i>निवेदक</span>`;

    const bgClass = isAdmin ? 'bg-danger-subtle bg-opacity-10 border-danger-subtle' : 'bg-primary-subtle bg-opacity-10 border-primary-subtle';

    return `
        <div class="p-2.5 rounded-3 border ${bgClass}">
            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap gap-1">
                <div class="d-flex align-items-center gap-1.5">
                    ${badgeHtml}
                    <strong class="text-dark small">${escapeHtml(remark.sender_name)}</strong>
                    ${isMe ? '<span class="text-muted extra-small" style="font-size:0.68rem;">(तपाईं)</span>' : ''}
                </div>
                <span class="text-muted extra-small" style="font-size: 0.7rem;" title="${escapeHtml(remark.created_at_formatted)}">${escapeHtml(remark.created_at_human || remark.created_at_formatted)}</span>
            </div>
            <p class="mb-0 text-dark small" style="white-space: pre-line; word-break: break-word;">${escapeHtml(remark.message)}</p>
        </div>
    `;
}

function loadRemarks() {
    const thread = document.getElementById('remarksThread');
    if (!thread) return;

    fetch(remarksUrl, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) return;
        if (!data.remarks || data.remarks.length === 0) {
            thread.innerHTML = `
                <div class="text-center text-muted small py-4">
                    <i data-lucide="message-square" style="width:28px;height:28px;" class="d-block mb-1.5 text-secondary opacity-50 mx-auto"></i>
                    कुनै पनि टिप्पणी वा संवाद सुरु भएको छैन। तलबाट पहिलो टिप्पणी लेख्नुहोस्।
                </div>
            `;
        } else {
            thread.innerHTML = data.remarks.map(renderRemarkItem).join('');
            thread.scrollTop = thread.scrollHeight;
        }
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    })
    .catch(err => {
        thread.innerHTML = `<div class="text-danger small py-3 text-center">टिप्पणी लोड गर्न सकिएन। पुनः प्रयास गर्नुहोस्।</div>`;
    });
}

function submitRemark(e) {
    e.preventDefault();
    const input = document.getElementById('remarkMessageInput');
    const submitBtn = document.getElementById('remarkSubmitBtn');
    const message = input.value.trim();
    if (!message) return;

    submitBtn.disabled = true;

    fetch(remarksStoreUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ message: message })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        if (data.success && data.remark) {
            input.value = '';
            const charCount = document.getElementById('remarkCharCount');
            if (charCount) charCount.textContent = '० / १०००';

            const thread = document.getElementById('remarksThread');
            if (thread.innerHTML.includes('कुनै पनि टिप्पणी')) {
                thread.innerHTML = '';
            }
            thread.insertAdjacentHTML('beforeend', renderRemarkItem(data.remark));
            thread.scrollTop = thread.scrollHeight;
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        } else {
            alert(data.message || 'त्रुटि: सन्देश पठाउन सकिएन।');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        alert('सर्भरसँग सम्पर्क हुन सकेन। कृपया पुनः प्रयास गर्नुहोस्।');
    });
}
</script>
@endpush
