@extends('layouts.citizen', ['pageTitle' => 'प्रमाणित कागजात तथा प्रमाणपत्रहरू'])

@section('content')
<!-- Page Header Banner -->
<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #05264E 0%, #003893 100%); color: #ffffff; border-radius: 14px;">
    <div class="card-body p-4 p-lg-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 54px; height: 54px; background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(4px);">
                    <i data-lucide="award" style="width: 30px; height: 30px; color: #93c5fd;"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="barhadashi-badge-pill"><i data-lucide="shield-check" style="width:13px;height:13px;"></i> आधिकारिक सेवा</span>
                        <span class="text-white-50 small">बाह्रदशी गाउँपालिका नागरिक पोर्टल</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">प्रमाणित कागजात तथा प्रमाणपत्रहरू</h3>
                    <p class="mb-0 text-white-50 small">
                        गाउँपालिकाबाट विधिवत् स्वीकृत भई जारी गरिएका आधिकारिक प्रमाणपत्र, सिफारिस पत्र तथा निर्णय कागजातहरू
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="text-end d-none d-md-block">
                    <span class="d-block small text-white-50">जारी गरिएका कुल कागजात</span>
                    <span class="fw-bold fs-4 text-white font-monospace">{{ $totalApprovedCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-3 p-lg-4">
        <form action="{{ route('citizen.approved-documents.index') }}" method="GET" class="row g-2 align-items-end">
            <!-- Search Input -->
            <div class="col-12 col-md-5">
                <label for="search" class="form-label small fw-semibold text-secondary mb-1">
                    <i data-lucide="search" style="width: 14px; height: 14px;" class="me-1"></i> कागजात द्रुत खोज (Search):
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i data-lucide="hash" class="text-muted" style="width: 15px; height: 15px;"></i></span>
                    <input type="text" 
                           name="search" 
                           id="search" 
                           class="form-control border-start-0" 
                           placeholder="यूनिक ID (उदा. ABC123), निवेदन नं, वा सेवाको नाम..." 
                           value="{{ request('search') }}"
                           autocomplete="off">
                </div>
            </div>

            <!-- Department Filter -->
            <div class="col-6 col-md-3">
                <label for="department_id" class="form-label small fw-semibold text-secondary mb-1">
                    <i data-lucide="building-2" style="width: 14px; height: 14px;" class="me-1"></i> विभाग / शाखा:
                </label>
                <select name="department_id" id="department_id" class="form-select">
                    <option value="">सबै विभागहरू</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year Filter -->
            <div class="col-6 col-md-2">
                <label for="year" class="form-label small fw-semibold text-secondary mb-1">
                    <i data-lucide="calendar" style="width: 14px; height: 14px;" class="me-1"></i> वर्ष (Year):
                </label>
                <select name="year" id="year" class="form-select">
                    <option value="">सबै मिति</option>
                    @for($y = (int) date('Y'); $y >= (int) date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i data-lucide="search" class="me-1" style="width: 15px; height: 15px;"></i> खोज्नुहोस्
                </button>
                @if(request()->hasAny(['search', 'department_id', 'year']))
                    <a href="{{ route('citizen.approved-documents.index') }}" class="btn btn-outline-secondary px-3" title="फिल्टर रिसेट">
                        <i data-lucide="rotate-ccw" style="width: 15px; height: 15px;"></i>
                    </a>
                @endif
            </div>
        </form>

        @if(request('search'))
            <div class="mt-2 pt-2 border-top d-flex align-items-center gap-2 small text-muted">
                <span>खोज परिणाम: <strong>"{{ request('search') }}"</strong></span>
                <span class="badge bg-light text-dark border">{{ $documents->total() }} कागजात भेटियो</span>
                <a href="{{ route('citizen.approved-documents.index') }}" class="text-danger text-decoration-none ms-2">
                    <i data-lucide="x" style="width: 12px; height: 12px;"></i> खोज हटाउनुहोस्
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Documents Results List / Grid -->
@if($documents->count() > 0)
    <div class="row g-3 mb-4">
        @foreach($documents as $app)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border shadow-sm rounded-3 overflow-hidden position-relative hover-shadow transition-all" style="border-top: 4px solid #053775 !important;">
                    
                    <!-- Card Top Header -->
                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold small">
                                <i data-lucide="check-circle" style="width: 12px; height: 12px;" class="me-1"></i>स्वीकृत
                            </span>
                        </div>
                        @if($app->certificate_number)
                            <span class="badge bg-light text-dark font-monospace border fw-bold px-2 py-1" title="Unique Certificate ID">
                                ID: <span class="text-primary">{{ $app->certificate_number }}</span>
                            </span>
                        @endif
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3">
                        <div class="small text-muted mb-1">
                            <i data-lucide="building" style="width: 13px; height: 13px;" class="me-1"></i>
                            {{ $app->service->department->name ?? 'गाउँपालिका' }}
                        </div>

                        <h6 class="fw-bold text-dark mb-2 text-truncate" title="{{ $app->approved_document_name ?: $app->service->name }}">
                            {{ $app->approved_document_name ?: ($app->service->name . ' — प्रमाणपत्र') }}
                        </h6>

                        <div class="bg-light p-2.5 rounded-2 small mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">निवेदन नम्बर:</span>
                                <span class="font-monospace fw-semibold text-dark">{{ $app->application_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">सेवाको नाम:</span>
                                <span class="fw-semibold text-truncate ms-2" style="max-width: 180px;">{{ $app->service->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">जारी मिति:</span>
                                <span class="fw-semibold text-dark">
                                    {{ $app->issued_at ? $app->issued_at->format('M d, Y') : ($app->processed_at ? $app->processed_at->format('M d, Y') : 'N/A') }}
                                </span>
                            </div>
                        </div>

                        @if($app->admin_remarks)
                            <p class="small text-muted fst-italic mb-3 text-truncate" title="{{ $app->admin_remarks }}">
                                <i data-lucide="message-square" style="width: 12px; height: 12px;" class="me-1"></i>
                                "{{ Str::limit($app->admin_remarks, 65) }}"
                            </p>
                        @endif
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="card-footer bg-white border-top p-2.5">
                        <div class="d-flex gap-2">
                            @if($app->hasApprovedDocument())
                                <a href="{{ route('citizen.approved-documents.view', $app) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-outline-primary flex-fill fw-semibold">
                                    <i data-lucide="eye" class="me-1" style="width: 14px; height: 14px;"></i> हेर्नुहोस्
                                </a>
                                <a href="{{ route('citizen.approved-documents.download', $app) }}" 
                                   class="btn btn-sm btn-primary flex-fill fw-semibold">
                                    <i data-lucide="download" class="me-1" style="width: 14px; height: 14px;"></i> डाउनलोड
                                </a>
                            @else
                                <a href="{{ route('citizen.applications.show', $app) }}" 
                                   class="btn btn-sm btn-outline-primary flex-fill fw-semibold">
                                    <i data-lucide="file-text" class="me-1" style="width: 14px; height: 14px;"></i> विवरण हेर्नुहोस्
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-2">
        {{ $documents->links() }}
    </div>
@else
    <!-- Zero / Empty State -->
    <div class="card border-0 shadow-sm text-center py-5 px-3" style="border-radius: 12px;">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3 mx-auto" style="width: 72px; height: 72px;">
            <i data-lucide="file-question" style="width: 36px; height: 36px; color: #94a3b8;"></i>
        </div>
        @if(request()->hasAny(['search', 'department_id', 'year']))
            <h5 class="fw-bold text-dark mb-1">खोज अनुसार कुनै कागजात फेला परेन</h5>
            <p class="text-muted small mb-3">
                तपाईंले खोज्नुभएको शब्द (<strong>"{{ request('search') }}"</strong>) वा फिल्टर अनुसार कुनै पनि प्रमाणित कागजात फेला परेन। कृपया अर्को नम्बर वा नाम जाँच गर्नुहोस्।
            </p>
            <div>
                <a href="{{ route('citizen.approved-documents.index') }}" class="btn btn-sm btn-outline-primary px-3">
                    <i data-lucide="rotate-ccw" class="me-1"></i> सबै कागजातहरू देखाउनुहोस्
                </a>
            </div>
        @else
            <h5 class="fw-bold text-dark mb-1">अहिलेसम्म कुनै प्रमाणित कागजात जारी भएको छैन</h5>
            <p class="text-muted small mb-3" style="max-width: 480px; margin: 0 auto;">
                तपाईंले पेश गर्नुभएको निवेदनहरू प्रशासकबाट स्वीकृत भएपछि जारी गरिएका आधिकारिक प्रमाणपत्र तथा कागजातहरू यस खण्डमा उपलब्ध हुनेछन्।
            </p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('citizen.applications.index') }}" class="btn btn-sm btn-outline-primary px-3">
                    <i data-lucide="file-search" class="me-1"></i> मेरा निवेदनहरूको स्थिति हेर्नुहोस्
                </a>
                <a href="{{ route('citizen.services.index') }}" class="btn btn-sm btn-primary px-3">
                    <i data-lucide="plus-circle" class="me-1"></i> नयाँ सेवाको लागि आवेदन दिनुहोस्
                </a>
            </div>
        @endif
    </div>
@endif
@endsection
