@extends('layouts.citizen', ['pageTitle' => $department->name . ' — कार्यविधि तथा योजनाहरू'])

@section('content')
<div class="mb-3">
    <a href="{{ route('citizen.branches.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> सम्पूर्ण शाखाहरूमा फर्कनुहोस्
    </a>
</div>

<!-- Header Card -->
<div class="card border-0 shadow-sm mb-4 bg-white rounded-4 overflow-hidden">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary text-white font-monospace px-3 py-1">
                        <i data-lucide="building" class="me-1" style="width: 14px; height: 14px;"></i> {{ $branchMeta['code'] }}
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                        सक्रिय शाखा
                    </span>
                </div>
                <h2 class="fw-bold text-dark mb-2">{{ $department->name }}</h2>
                <p class="text-secondary leading-relaxed mb-4" style="font-size: 1.05rem;">
                    {{ $department->description ?? 'यस शाखाबाट गाउँपालिका अन्तर्गतका तोकिएका नागरिक सेवा तथा योजनाहरूको व्यवस्थापन गरिन्छ।' }}
                </p>

                <div class="row g-3 bg-light p-3 rounded-3 border">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 text-dark">
                            <i data-lucide="map-pin" class="text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block">कार्यालय / कोठा स्थान:</span>
                                <span class="fw-bold">{{ $branchMeta['room'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 text-dark">
                            <i data-lucide="user-check" class="text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block">शाखा जिम्मेवार अधिकारी:</span>
                                <span class="fw-semibold">{{ $branchMeta['officer'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 text-dark">
                            <i data-lucide="clock" class="text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block">सेवा सञ्चालन समय:</span>
                                <span class="fw-semibold small">{{ $branchMeta['hours'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 text-dark">
                            <i data-lucide="phone-call" class="text-primary fs-5"></i>
                            <div>
                                <span class="text-muted extra-small d-block">सम्पर्क नम्बर / इमेल:</span>
                                <span class="fw-semibold small">{{ $branchMeta['phone'] }} | {{ $branchMeta['email'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-center">
                <div class="p-4 bg-primary-subtle rounded-4 border border-primary-subtle text-primary">
                    <i data-lucide="shield-check" style="width: 56px; height: 56px;" class="mb-2"></i>
                    <h6 class="fw-bold mb-2 text-dark">शाखा निवेदन प्रमाणीकरण</h6>
                    <p class="small text-secondary mb-3">यस शाखाबाट सम्पादित योजना वा निवेदनको आधिकारिकता स्थिति तुरुन्तै जाँच गर्नुहोस्।</p>
                    <a href="{{ route('citizen.verify.index', ['department_id' => $department->id]) }}" class="btn btn-primary w-100 fw-bold">
                        <i data-lucide="search-check" class="me-1"></i> यस शाखाको निवेदन Verify गर्नुहोस्
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Step by Step Workflow Guide for this Branch -->
<div class="card border-0 shadow-sm mb-4 bg-white rounded-4 p-4">
    <h5 class="fw-bold text-dark mb-4">
        <i data-lucide="git-pull-request" class="me-2 text-primary"></i>यस शाखामा जाने र काम गर्ने प्रक्रिया (Step-by-Step Procedure Flow)
    </h5>

    <div class="row g-3 position-relative">
        @foreach($branchMeta['steps'] as $index => $step)
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border p-3 rounded-3 bg-white position-relative shadow-sm hover-shadow">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 1rem;">
                            {{ $step['step'] }}
                        </span>
                        <span class="text-muted extra-small fw-semibold">चरणा {{ $step['step'] }}</span>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">{{ $step['title'] }}</h6>
                    <p class="small text-secondary mb-0 leading-relaxed">{{ $step['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- List of Yojanas / Services in this Branch -->
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h4 class="fw-bold mb-1 text-dark">यस शाखा अन्तर्गतका योजना तथा सेवाहरू</h4>
        <span class="text-muted small">नागरिकहरूले अनलाइन आवेदन दिन सक्ने सूचीकृत कार्यहरू</span>
    </div>
    <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">कुल {{ $department->services->count() }} सेवाहरू</span>
</div>

<div class="row g-4 mb-4">
    @forelse($department->services as $service)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="service-card h-100 d-flex flex-column justify-content-between shadow-sm border rounded-4 p-4 bg-white">
                <div>
                    <span class="badge bg-light text-primary border mb-2 font-monospace">{{ $branchMeta['room'] }}</span>
                    <h5 class="fw-bold text-dark mb-2">{{ $service->name }}</h5>
                    <p class="small text-muted mb-3 flex-grow-1">{{ Str::limit($service->description, 110) }}</p>

                    <div class="bg-light p-3 rounded-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">सरकारी दस्तुर:</span>
                            <span class="fw-bold text-success">{{ $service->fee > 0 ? 'रु. ' . number_format($service->fee, 2) : 'निःशुल्क' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">अनुमानित प्रशोधन समय:</span>
                            <span class="fw-semibold text-dark">{{ $service->processing_days }} कार्यदिन</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">आवश्यक कागजात:</span>
                            <span class="fw-semibold text-dark">{{ is_array($service->required_documents) ? count($service->required_documents) : 0 }} वटा</span>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('citizen.services.show', $service) }}" class="btn btn-outline-primary btn-sm fw-bold">
                        <i data-lucide="info" class="me-1"></i> विस्तृत कार्यविधि हेर्नुहोस्
                    </a>
                    <a href="{{ route('citizen.applications.create', ['service_id' => $service->id]) }}" class="btn btn-primary btn-sm fw-bold">
                        <i data-lucide="send" class="me-1"></i> अनलाइन आवेदन दिनुहोस्
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-4 border">
                <i data-lucide="folder-open" class="text-muted fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold text-muted">यस शाखामा हाल कुनै पनि योजना/सेवा सूचीकृत गरिएको छैन।</h6>
            </div>
        </div>
    @endforelse
</div>
@endsection
