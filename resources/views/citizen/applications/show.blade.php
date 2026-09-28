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
            <a href="{{ route('citizen.payments.receipt', $application) }}" class="btn btn-sm btn-outline-success">
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
    <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%); color: #ffffff; border-radius: 12px;">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 50px; height: 50px; background: rgba(255, 255, 255, 0.2);">
                        <i data-lucide="award" style="width: 28px; height: 28px; color: #fef08a;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-warning text-dark fw-bold small">आधिकारिक प्रमाणपत्र जारी भएको</span>
                            @if($application->certificate_number)
                                <span class="badge bg-white text-dark font-monospace fw-bold px-2 py-0.5" style="letter-spacing: 0.5px;">
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
                    <a href="{{ route('citizen.approved-documents.view', $application) }}" target="_blank" class="btn btn-light fw-bold px-3">
                        <i data-lucide="eye" class="me-1"></i> कागजात हेर्नुहोस्
                    </a>
                    <a href="{{ route('citizen.approved-documents.download', $application) }}" class="btn btn-warning fw-bold px-3 text-dark">
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

                <h6 class="fw-bold text-dark mb-3"><i data-lucide="file-check" class="me-2 text-primary"></i>अपलोड गरिएका कागजातहरू</h6>
                @if($application->documents->count() > 0)
                    <div class="list-group mb-4">
                        @foreach($application->documents as $doc)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="file-text" class="text-danger fs-4"></i>
                                    <div>
                                        <span class="fw-semibold text-dark small d-block">{{ $doc->document_name }}</span>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i data-lucide="eye"></i> हेर्नुहोस्
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-3 bg-light rounded text-muted small mb-4">यस निवेदनको लागि कुनै पनि कागजात अपलोड गरिएको छैन।</div>
                @endif
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
