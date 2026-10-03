@extends('layouts.citizen', ['pageTitle' => 'शाखा तथा योजना निवेदन प्रमाणीकरण'])

@section('content')
<!-- Verification Hero -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #05264E 0%, #003893 100%); color: #ffffff; border-radius: 14px;">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="mb-3">
                    <span class="barhadashi-badge-pill">
                        <i data-lucide="shield-check" style="width: 15px; height: 15px;"></i> आधिकारिक डिजिटल प्रमाणीकरण प्रणाली
                    </span>
                </div>
                <h2 class="fw-extrabold text-white mb-2" style="letter-spacing: -0.5px;">आधिकारिक शाखा तथा योजना प्रमाणीकरण</h2>
                <p class="text-white-50 leading-relaxed mb-0" style="font-size: 1.05rem;">
                    आफ्नो निवेदन नम्बर (उदा: GOV-20260928-00001) वा जारी भएको प्रमाणपत्र ID प्रविष्ट गरी निवेदनको सत्यता, तोकिएको शाखा, कार्य प्रगति र आधिकारिक प्रमाणीकरण स्थिति तुरुन्तै जाँच गर्नुहोस्।
                </p>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg">
                    <i data-lucide="badge-check" style="width: 72px; height: 72px; color: #93c5fd;"></i>
                    <h5 class="fw-bold text-white mt-2 mb-0">100% भरपर्दो प्रमाणीकरण</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search & Input Card -->
<div class="card border-0 shadow-sm bg-white rounded-4 p-4 mb-4">
    <form action="{{ route('citizen.verify.index') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-12 col-md-5">
            <label class="form-label fw-bold text-dark mb-1"><i data-lucide="hash" class="me-1 text-primary"></i>निवेदन वा प्रमाणपत्र ID:</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i data-lucide="search" class="text-muted"></i></span>
                <input type="text" name="query" class="form-control bg-light fw-bold font-monospace" placeholder="उदा: GOV-20260928-00001 वा XYZ123" value="{{ request('query', $searchQuery) }}" required>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label fw-bold text-dark mb-1"><i data-lucide="building" class="me-1 text-primary"></i>विषयगत शाखा (Department):</label>
            <select name="department_id" class="form-select bg-light">
                <option value="">सबै विषयगत शाखाहरू</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3">
                <i data-lucide="search-check" class="me-1"></i> प्रमाणीकरण गर्नुहोस्
            </button>
        </div>
    </form>
</div>

<!-- Verification Results -->
@if(request()->filled('query'))
    @if($application)
        @php
            $controller = new \App\Http\Controllers\Citizen\BranchController();
            $meta = $controller->getBranchMetadata($application->service->department);
        @endphp
        <!-- Verified Result Display -->
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4" id="printableVerificationCard">
            <!-- Official Verification Seal Header -->
            <div class="p-4 text-white d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #053775 0%, #003893 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center p-2 shadow-sm" style="width: 54px; height: 54px;">
                        <i data-lucide="shield-check" class="text-primary" style="width: 36px; height: 36px;"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="barhadashi-badge-pill"><i data-lucide="check-circle" class="me-1" style="width: 12px; height: 12px;"></i>प्रमाणीकरण सफल (VERIFIED)</span>
                            <span class="text-white-50 extra-small">डिजिटल छाप पुष्टि</span>
                        </div>
                        <h4 class="fw-bold text-white mb-0">बाह्रदशी गाउँपालिका आधिकारिक निवेदन प्रमाणीकरण पत्र</h4>
                    </div>
                </div>

                <div class="d-flex gap-2 print-hide">
                    <button onclick="window.print()" class="btn btn-light btn-sm fw-bold">
                        <i data-lucide="printer" class="me-1"></i> प्रिन्ट / PDF सेभ गर्नुहोस्
                    </button>
                </div>
            </div>

            <div class="card-body p-4 p-md-5 bg-white">
                <!-- Status Timeline Tracker -->
                <div class="bg-light p-4 rounded-4 mb-4 border">
                    <h6 class="fw-bold text-center mb-3 text-dark">शाखा कार्यप्रगति तथा प्रमाणीकरण स्थिति</h6>
                    <ul class="status-tracker mb-0">
                        <li class="step completed">
                            <div class="step-icon"><i data-lucide="send"></i></div>
                            <div class="step-label">दर्ता भएको</div>
                        </li>
                        <li class="step {{ in_array($application->status, ['under_review', 'approved', 'completed']) ? 'completed' : ($application->status == 'pending' ? 'active' : '') }}">
                            <div class="step-icon"><i data-lucide="search"></i></div>
                            <div class="step-label">शाखा रुजु</div>
                        </li>
                        <li class="step {{ ($application->payment && $application->payment->status === 'completed') || in_array($application->status, ['approved', 'completed']) ? 'completed' : '' }}">
                            <div class="step-icon"><i data-lucide="credit-card"></i></div>
                            <div class="step-label">राजस्व चुक्ता</div>
                        </li>
                        @if($application->status == 'rejected')
                            <li class="step rejected">
                                <div class="step-icon"><i data-lucide="x-circle"></i></div>
                                <div class="step-label">अस्वीकृत</div>
                            </li>
                        @else
                            <li class="step {{ in_array($application->status, ['approved', 'completed']) ? 'completed' : '' }}">
                                <div class="step-icon"><i data-lucide="award"></i></div>
                                <div class="step-label">शाखा स्वीकृत / जारी</div>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- Info Grid -->
                <div class="row g-4 mb-4">
                    <!-- Branch Info -->
                    <div class="col-12 col-md-6">
                        <div class="p-3.5 border rounded-3 bg-light-subtle h-100">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i data-lucide="building-2" class="me-1"></i>जिम्मेवार विषयगत शाखा (Branch Details)
                            </h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">सम्बन्धित मन्त्रालय / विभाग:</span>
                                <span class="fw-bold text-dark fs-6">{{ $application->service->department->name ?? 'नेपाल सरकार' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">कार्यालय स्थान / कोठा:</span>
                                <span class="fw-semibold text-dark"><i data-lucide="map-pin" class="me-1 text-primary"></i>{{ $meta['room'] }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">शाखा जिम्मेवार अधिकृत:</span>
                                <span class="fw-semibold text-dark">{{ $meta['officer'] }}</span>
                            </div>
                            <div>
                                <span class="text-muted small d-block">शाखा सम्पर्क:</span>
                                <span class="small text-dark">{{ $meta['phone'] }} | {{ $meta['email'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Application Info -->
                    <div class="col-12 col-md-6">
                        <div class="p-3.5 border rounded-3 bg-light-subtle h-100">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i data-lucide="file-text" class="me-1"></i>निवेदन तथा आवेदक विवरण
                            </h6>
                            <div class="mb-2">
                                <span class="text-muted small d-block">निवेदन नम्बर (Ref No):</span>
                                <span class="fw-bold font-monospace text-dark fs-6">#{{ $application->application_number }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">योजना / सेवाको नाम:</span>
                                <span class="fw-bold text-dark">{{ $application->service->name ?? 'N/A' }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted small d-block">आवेदकको नाम:</span>
                                <span class="fw-semibold text-dark">{{ $application->applicant_name }}</span>
                            </div>
                            <div class="mb-0">
                                <span class="text-muted small d-block">पेश गरिएको मिति:</span>
                                <span class="fw-semibold text-dark">{{ $application->submitted_at ? $application->submitted_at->format('M d, Y h:i A') : $application->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Digital Certificate & Seal Details if Approved -->
                @if($application->hasApprovedDocument() || $application->status === 'approved')
                    <div class="p-4 border border-primary-subtle rounded-4 bg-primary-subtle mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <span class="badge bg-primary text-white mb-1">आधिकारिक प्रमाणपत्र उपलब्ध</span>
                                <h5 class="fw-bold text-dark mb-1">{{ $application->approved_document_name ?: ($application->service->name . ' — प्रमाणपत्र') }}</h5>
                                <div class="small text-secondary">
                                    प्रमाणपत्र ID: <strong class="font-monospace text-primary">{{ $application->certificate_number }}</strong> &bull;
                                    जारी मिति: <strong>{{ $application->issued_at ? $application->issued_at->format('M d, Y') : ($application->processed_at ? $application->processed_at->format('M d, Y') : 'N/A') }}</strong>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('citizen.approved-documents.certificate', $application) }}" target="_blank" class="btn btn-primary fw-bold shadow-sm">
                                    <i data-lucide="award" class="me-1"></i> आधिकारिक प्रमाणपत्र हेर्नुहोस्
                                </a>
                                <a href="{{ route('citizen.approved-documents.certificate.pdf', $application) }}" class="btn btn-outline-primary fw-bold">
                                    <i data-lucide="download" class="me-1"></i> PDF डाउनलोड
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Official Footer Seal -->
                <div class="border-top pt-4 mt-4 text-center">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Emblem" style="height: 44px; width: auto;">
                        <div class="text-start">
                            <h6 class="fw-bold mb-0 text-dark">बाह्रदशी गाउँपालिका, गाउँ कार्यपालिकाको कार्यालय</h6>
                            <span class="extra-small text-muted">डिजिटल प्रमाणीकरण छाप • बाह्रदशी गाउँपालिका, झापा</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Not Found State -->
        <div class="card border-0 shadow-sm bg-white rounded-4 p-5 text-center">
            <i data-lucide="alert-triangle" class="text-warning display-4 mb-3 d-block mx-auto"></i>
            <h5 class="fw-bold text-dark mb-2">माफ गर्नुहोला! प्रविष्ट गरिएको विवरण भेटिएन।</h5>
            <p class="text-secondary small mb-4">
                कृपया तपाईंको निवेदन नम्बर (उदा: GOV-20260928-00001) वा प्रमाणपत्र कोड सही तरिकाले प्रविष्ट गरिएको छ भनी पुनः जाँच गर्नुहोस्।
            </p>
            <div>
                <a href="{{ route('citizen.applications.index') }}" class="btn btn-outline-primary fw-bold px-4">
                    <i data-lucide="file-text" class="me-1"></i> मेरो निवेदनहरूबाट नम्बर हेर्नुहोस्
                </a>
            </div>
        </div>
    @endif
@else
    <!-- Quick Verification Guidance (No pre-listed statuses, status only shows upon specific document search) -->
    <div class="card border-0 shadow-sm bg-white rounded-4 p-4">
        <h5 class="fw-bold text-dark mb-3"><i data-lucide="help-circle" class="me-2 text-primary"></i>प्रमाणीकरण कसरी गर्ने?</h5>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1"><i data-lucide="hash" class="me-1 text-primary"></i>१. ID वा नम्बर प्रविष्ट गर्नुहोस्</div>
                    <p class="small text-muted mb-0">माथिको खोजी बाकसमा तपाईंको निवेदन नम्बर (उदा: GOV-XXXX) वा जारी भएको ६-अङ्कको प्रमाणपत्र ID (उदा: KABD203) प्रविष्ट गर्नुहोस्।</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1"><i data-lucide="building" class="me-1 text-primary"></i>२. शाखा छनोट (ऐच्छिक)</div>
                    <p class="small text-muted mb-0">यदि आवश्यक भए सम्बन्धित विषयगत शाखा छान्नुहोस् र 'प्रमाणीकरण गर्नुहोस्' बटनमा थिच्नुहोस्।</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="p-3 bg-light rounded-3 h-100 border">
                    <div class="fw-bold text-dark mb-1"><i data-lucide="shield-check" class="me-1 text-primary"></i>३. आधिकारिक स्थिति तथा छाप</div>
                    <p class="small text-muted mb-0">तपाईंको सो विशिष्ट कागजातको आधिकारिक प्रमाणीकरण स्थिति, डिजिटल छाप र आधिकारिक पत्र तुरुन्तै देखिनेछ।</p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
