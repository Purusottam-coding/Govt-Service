@extends('layouts.citizen', ['pageTitle' => 'निवेदन विवरण तथा स्थिति'])

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('citizen.applications.index') }}" class="btn btn-sm btn-outline-secondary">
            <i data-lucide="arrow-left" class="me-1"></i> मेरा निवेदनहरूमा फर्कनुहोस्
        </a>
        @if($application->canBeEdited())
            <a href="{{ route('citizen.applications.edit', $application) }}" class="btn btn-sm btn-outline-warning">
                <i data-lucide="pencil" class="me-1"></i> सम्पादन गर्नुहोस्
            </a>
        @endif
        @if($application->canBeDeleted())
            <form action="{{ route('citizen.applications.destroy', $application) }}" method="POST" class="d-inline" onsubmit="return confirm('के तपाईं पक्का यो निवेदन हटाउन चाहनुहुन्छ? यो कार्य फिर्ता गर्न सकिँदैन।');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i data-lucide="trash-2" class="me-1"></i> हटाउनुहोस्
                </button>
            </form>
        @endif
    </div>
    <div>
        @if($application->payment && $application->payment->status === 'completed')
            <a href="{{ route('citizen.payments.receipt', $application) }}" class="btn btn-sm btn-outline-primary">
                <i data-lucide="receipt" class="me-1"></i> भुक्तानी रसिद हेर्नुहोस्
            </a>
        @elseif($application->payment && $application->payment->status === 'pending')
            <span class="badge bg-primary text-white px-3 py-2">
                <i data-lucide="clock-3" class="me-1"></i> भुक्तानी प्रमाण प्रमाणीकरणमा
            </span>
        @elseif(($application->service->fee ?? 0) > 0)
            <a href="{{ route('citizen.payments.create', $application) }}" class="btn btn-sm btn-primary">
                <i data-lucide="credit-card" class="me-1"></i> दस्तुर भुक्तानी गर्नुहोस् (रु. {{ number_format($application->service->fee, 2) }})
            </a>
        @endif
    </div>
</div>

<!-- Visual Status Tracker -->
<div class="card mb-4 p-4">
    <h6 class="fw-bold text-center mb-3">निवेदन प्रगति स्थिति</h6>

    <ul class="status-tracker">
        <li class="step {{ in_array($application->status, ['pending', 'under_review', 'approved', 'completed']) ? 'completed' : '' }}">
            <div class="step-icon"><i data-lucide="send-check"></i></div>
            <div class="step-label">पेश गरिएको</div>
        </li>
        <li class="step {{ in_array($application->status, ['under_review', 'approved', 'completed']) ? 'completed' : ($application->status == 'pending' ? 'active' : '') }}">
            <div class="step-icon"><i data-lucide="search"></i></div>
            <div class="step-label">छानबिनमा</div>
        </li>
        @if($application->status == 'rejected')
            <li class="step rejected">
                <div class="step-icon"><i data-lucide="x-circle"></i></div>
                <div class="step-label">अस्वीकृत</div>
            </li>
        @else
            <li class="step {{ in_array($application->status, ['approved', 'completed']) ? 'completed' : '' }}">
                <div class="step-icon"><i data-lucide="check"></i></div>
                <div class="step-label">स्वीकृत</div>
            </li>
            <li class="step {{ $application->status == 'completed' ? 'completed' : '' }}">
                <div class="step-icon"><i data-lucide="award"></i></div>
                <div class="step-label">सम्पन्न</div>
            </li>
        @endif
    </ul>
</div>

@if($application->hasApprovedDocument())
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #05264E 0%, #003893 100%); color: #ffffff; border-radius: 12px;">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(4px);">
                        <i data-lucide="award" style="width: 28px; height: 28px; color: #93c5fd;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="barhadashi-badge-pill"><i data-lucide="award" style="width:13px;height:13px;"></i> आधिकारिक प्रमाणपत्र जारी भएको</span>
                            @if($application->certificate_number)
                                <span class="badge bg-white text-primary font-monospace fw-bold px-2 py-0.5" style="letter-spacing: 0.5px;">
                                    ID: {{ $application->certificate_number }}
                                </span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1 text-white">{{ $application->approved_document_name }}</h5>
                        <p class="mb-0 text-white-50 small">
                            जारी मिति: {{ $application->issued_at ? $application->issued_at->format('M d, Y') : ($application->processed_at ? $application->processed_at->format('M d, Y') : 'N/A') }} &bull; फाइल ढाँचा: <span class="text-uppercase fw-bold text-white">{{ $application->approved_document_type }}</span>
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('citizen.approved-documents.view', $application) }}" target="_blank" class="btn btn-outline-light fw-bold px-3">
                        <i data-lucide="eye" class="me-1"></i> कागजात हेर्नुहोस्
                    </a>
                    <a href="{{ route('citizen.approved-documents.download', $application) }}" class="btn btn-light fw-bold px-3 text-primary">
                        <i data-lucide="download" class="me-1"></i> डाउनलोड गर्नुहोस्
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row g-4">
    <!-- Main Info -->
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2 text-primary"></i>निवेदन नं. #{{ $application->application_number }}</h6>
                <span class="badge-status {{ $application->getStatusBadgeClass() }}">{{ $application->getStatusLabel() }}</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">सेवाको नाम</span>
                        <span class="fw-bold text-dark">{{ $application->service->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">मन्त्रालय / विभाग</span>
                        <span class="fw-semibold">{{ $application->service->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <span class="text-muted small d-block">पेश गरेको मिति</span>
                        <span class="fw-semibold">{{ $application->submitted_at ? $application->submitted_at->format('M d, Y h:i A') : $application->created_at->format('M d, Y') }}</span>
                    </div>
                </div>

                @if($application->admin_remarks)
                    <div class="alert alert-info mb-4">
                        <h6 class="fw-bold mb-1"><i data-lucide="info" class="me-1"></i> प्रशासकीय टिप्पणी / सूचना:</h6>
                        <p class="mb-0 small" style="white-space: pre-line;">{{ $application->admin_remarks }}</p>
                    </div>
                @endif

                @php
                    $replacementDocs = $application->documents->filter(fn($d) => $d->isReplacementNeeded());
                @endphp

                @if($replacementDocs->count() > 0)
                    <div class="alert alert-warning border-warning shadow-sm mb-4">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i data-lucide="alert-triangle" class="text-danger fs-5"></i>
                            <h6 class="mb-0 fw-bold text-danger">ध्यानाकर्षण: कागजात प्रतिस्थापन आवश्यक छ</h6>
                        </div>
                        <p class="small text-dark mb-0">
                            प्रशासनले तपाईंको निवेदनमा केही कागजातहरू पुनः अपलोड गर्न अनुरोध गरेको छ। कृपया तल रातो चिन्ह लगाइएका कागजातमा नयाँ फाइल छानेर तत्काल प्रतिस्थापन (Replace) गर्नुहोस्।
                        </p>
                    </div>
                @endif

                <h6 class="fw-bold text-dark mb-3"><i data-lucide="file-check" class="me-2 text-primary"></i>अपलोड गरिएका कागजातहरू</h6>
                @if($application->documents->count() > 0)
                    <div class="list-group mb-4">
                        @foreach($application->documents as $doc)
                            <div class="list-group-item p-3 {{ $doc->isReplacementNeeded() ? 'border-danger bg-danger-subtle bg-opacity-10' : '' }}">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="file-text" class="text-danger fs-4"></i>
                                        <div>
                                            <span class="fw-bold text-dark small d-block">{{ $doc->document_name }}</span>
                                            @if($doc->isReplacementNeeded())
                                                <span class="badge bg-danger text-white extra-small"><i data-lucide="alert-circle" style="width: 10px; height: 10px;" class="me-1"></i>प्रतिस्थापन आवश्यक</span>
                                            @elseif($doc->replaced_at)
                                                <span class="badge bg-info-subtle text-info extra-small"><i data-lucide="check" style="width: 10px; height: 10px;" class="me-1"></i>नयाँ फाइल पेस भयो</span>
                                            @else
                                                <span class="text-muted extra-small">पेस गरिएको</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i data-lucide="eye"></i> हेर्नुहोस्
                                        </a>
                                    </div>
                                </div>

                                @if($doc->isReplacementNeeded())
                                    <div class="mt-3 p-3 bg-white border border-danger rounded-3">
                                        <div class="text-danger small fw-semibold mb-2">
                                            <i data-lucide="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                            प्रशासकीय कैफियत: <span class="fw-normal text-dark">{{ $doc->admin_feedback }}</span>
                                        </div>

                                        <form action="{{ route('citizen.applications.documents.replace', [$application, $doc]) }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                                            @csrf
                                            <div class="flex-grow-1">
                                                <input type="file" name="document_file" class="form-control form-control-sm @error('document_file') is-invalid @enderror" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                @error('document_file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold text-nowrap">
                                                <i data-lucide="upload" style="width: 13px; height: 13px;"></i> नयाँ फाइल अपलोड गर्नुहोस्
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded text-muted small mb-4">यस निवेदनको लागि कुनै पनि कागजात अपलोड गरिएको छैन।</div>
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
                    <h6 class="mb-0 fw-bold text-dark">प्रशासकीय संवाद तथा सोधपुछ (Live Remarks)</h6>
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
                        <textarea name="message" id="remarkMessageInput" rows="2" class="form-control form-control-sm" placeholder="प्रशासनलाई कुनै सोधपुछ वा जानकारी पठाउनुहोस्..." required maxlength="1000"></textarea>
                        <button type="submit" id="remarkSubmitBtn" class="btn btn-primary px-3 d-flex align-items-center gap-1 fw-bold">
                            <i data-lucide="send" style="width: 14px; height: 14px;"></i> <span>पठाउनुहोस्</span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted extra-small" style="font-size: 0.72rem;">* यो संवाद सम्बन्धित शाखा/प्रशासनले तुरुन्तै देख्नेछन्।</small>
                        <small class="text-muted extra-small" id="remarkCharCount" style="font-size: 0.72rem;">० / १०००</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="col-12 col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="credit-card" class="me-2 text-primary"></i>भुक्तानी तथा दस्तुर</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">आवश्यक दस्तुर:</span>
                    <span class="fw-bold text-dark">रु. {{ number_format($application->service->fee ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">भुक्तानी स्थिति:</span>
                    @if($application->payment)
                        @if($application->payment->status === 'pending')
                            <span class="badge bg-primary">प्रमाणीकरणमा</span>
                        @elseif($application->payment->status === 'completed')
                            <span class="badge bg-success">चुक्ता भएको</span>
                        @else
                            <span class="badge bg-danger">असफल</span>
                        @endif
                    @elseif(($application->service->fee ?? 0) > 0)
                        <span class="badge bg-primary text-white">बाँकी (बाँकी भुक्तानी)</span>
                    @else
                        <span class="badge bg-light text-muted">निःशुल्क</span>
                    @endif
                </div>

                @if(!$application->payment && ($application->service->fee ?? 0) > 0)
                    <a href="{{ route('citizen.payments.create', $application) }}" class="btn btn-primary w-100 fw-bold">
                        <i data-lucide="credit-card" class="me-1"></i> भुक्तानी गर्नुहोस्
                    </a>
                @endif

                @if($application->payment)
                    <hr>
                    <div class="small">
                        <div><strong>कारोबार नं (Transaction ID):</strong> {{ $application->payment->transaction_id }}</div>
                        <div><strong>भुक्तानी मिति:</strong> {{ $application->payment->paid_at ? $application->payment->paid_at->format('M d, Y') : '' }}</div>
                        @if($application->payment->payment_statement)
                            <div class="mt-2">
                                <a href="{{ Storage::url($application->payment->payment_statement) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-lucide="image" class="me-1"></i> भुक्तानी स्टेटमेन्ट हेर्नुहोस्
                                </a>
                            </div>
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
document.addEventListener('DOMContentLoaded', function () {
    loadRemarks();

    const remarkInput = document.getElementById('remarkMessageInput');
    const charCountEl = document.getElementById('remarkCharCount');
    if (remarkInput && charCountEl) {
        remarkInput.addEventListener('input', function() {
            charCountEl.textContent = this.value.length + ' / 1000';
        });
    }
});

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
                    कुनै पनि टिप्पणी वा संवाद सुरु भएको छैन। केही सोधपुछ वा जानकारी भए तलबाट लेख्नुहोस्।
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
