@extends('layouts.citizen', ['pageTitle' => 'गाउँपालिका शाखा तथा योजना निर्देशिका'])

@section('content')
<!-- Hero Banner -->
<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff; border-radius: 14px;">
    <div class="card-body p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="mb-3">
                    <span class="barhadashi-badge-pill">
                        <i data-lucide="building-2" style="width: 15px; height: 15px;"></i> गाउँपालिका विषयगत शाखा पोर्टल
                    </span>
                </div>
                <h2 class="fw-extrabold text-white mb-2" style="letter-spacing: -0.5px;">विषयगत शाखा तथा योजना निर्देशिका</h2>
                <p class="text-white-50 leading-relaxed mb-4" style="font-size: 1.05rem;">
                    नागरिकहरूले कुन योजना तथा कामको लागि कुन शाखामा जाने, के कागजात बुझाउने र कसरी प्रमाणीकरण गर्ने सम्बन्धी सम्पूर्ण विस्तृत विवरण यहाँ प्राप्त गर्न सक्नुहुन्छ।
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('citizen.verify.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 shadow-sm">
                        <i data-lucide="shield-check" class="me-1"></i> निवेदन / शाखा प्रमाणीकरण ट्र्याक गर्नुहोस्
                    </a>
                    <a href="{{ route('citizen.services.index') }}" class="btn btn-outline-light px-4 py-2">
                        <i data-lucide="list-filter" class="me-1"></i> सम्पूर्ण सरकारी सेवाहरू
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg">
                    <i data-lucide="git-branch" style="width: 72px; height: 72px; color: #93c5fd;"></i>
                    <h5 class="fw-bold text-white mt-2 mb-1">गाउँपालिका शाखाहरू</h5>
                    <p class="text-white-50 extra-small mb-0">पारदर्शी सेवा प्रवाह र छिटो प्रशोधन</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="card mb-4 p-3 bg-white border-0 shadow-sm">
    <form action="{{ route('citizen.branches.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-light"><i data-lucide="search" class="text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 bg-light" placeholder="शाखाको नाम वा योजनाको आधारमा खोज्नुहोस् (उदा: योजना, राजस्व, दर्ता, भवन)..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i data-lucide="search" class="me-1"></i> खोज्नुहोस्
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('citizen.branches.index') }}" class="btn btn-outline-secondary" title="पुनः सेट गर्नुहोस्"><i data-lucide="x"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Branch Cards Grid -->
<div class="row g-4 mb-5">
    @forelse($departments as $dept)
        @php
            $controller = new \App\Http\Controllers\Citizen\BranchController();
            $meta = $controller->getBranchMetadata($dept);
        @endphp
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all rounded-4 overflow-hidden position-relative">
                <div class="card-header bg-white pt-4 px-4 pb-2 border-0 d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold mb-2">
                            <i data-lucide="hash" class="me-0.5"></i> {{ $meta['code'] }}
                        </span>
                        <h5 class="fw-bold text-dark mb-0">{{ $dept->name }}</h5>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1">
                        {{ $dept->services_count }} योजना / सेवाहरू
                    </span>
                </div>
                <div class="card-body px-4 py-2 d-flex flex-column justify-content-between">
                    <p class="text-secondary small mb-3 flex-grow-1">
                        {{ Str::limit($dept->description, 110, '...') }}
                    </p>

                    <div class="bg-light p-3 rounded-3 mb-3 small">
                        <div class="d-flex align-items-center gap-2 mb-1 text-dark fw-semibold">
                            <i data-lucide="map-pin" class="text-primary" style="width: 16px; height: 16px;"></i>
                            <span>{{ $meta['room'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted extra-small">
                            <i data-lucide="user-check" class="text-secondary" style="width: 14px; height: 14px;"></i>
                            <span>{{ $meta['officer'] }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted extra-small mt-1">
                            <i data-lucide="phone" class="text-secondary" style="width: 14px; height: 14px;"></i>
                            <span>{{ $meta['phone'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white p-4 pt-0 border-0">
                    <a href="{{ route('citizen.branches.show', $dept) }}" class="btn btn-outline-primary w-100 fw-bold rounded-3 d-flex justify-content-between align-items-center">
                        <span>योजना तथा कार्यविधि हेर्नुहोस्</span>
                        <i data-lucide="arrow-right" class="ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                <i data-lucide="building" class="text-muted fs-1 mb-2 d-block"></i>
                <h5 class="fw-bold text-dark">कुनै पनि शाखा भेटिएन।</h5>
                <p class="small text-muted">कृपया अर्कै शब्द प्रयोग गरी पुनः खोज्नुहोस्।</p>
            </div>
        </div>
    @endforelse
</div>

<!-- How to Use Guidance Card for Citizen -->
<div class="card border-0 shadow-sm bg-white rounded-4 p-4 mb-4">
    <h5 class="fw-bold text-dark mb-3"><i data-lucide="compass" class="me-2 text-primary"></i>नागरिक मार्गदर्शन: शाखा चयन र काम गर्ने तरिका</h5>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle h-100">
                <div class="fw-bold text-primary mb-1"><i data-lucide="search" class="me-1"></i> १. शाखा र योजना छनोट</div>
                <p class="small text-secondary mb-0">आफ्नो आवश्यकता अनुसार माथिका शाखाहरू मध्ये उपयुक्त शाखा चयन गर्नुहोस् र उपलब्ध योजना तथा सेवाहरू हेर्नुहोस्।</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 bg-info-subtle rounded-3 border border-info-subtle h-100">
                <div class="fw-bold text-info-emphasis mb-1"><i data-lucide="file-check-2" class="me-1"></i> २. आवश्यक कागजात र कोठा रुजु</div>
                <p class="small text-secondary mb-0">शाखा विवरणमा तोकिएको कोठा नं., आवश्यक कागजातको सूची र दस्तुर विवरण राम्ररी अध्ययन गरी अनलाइन वा प्रत्यक्ष पेश गर्नुहोस्।</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="p-3 bg-light rounded-3 border border-secondary-subtle h-100">
                <div class="fw-bold text-primary mb-1"><i data-lucide="badge-check" class="me-1"></i> ३. विश्वसनीय प्रमाणीकरण</div>
                <p class="small text-secondary mb-0">आवेदन दर्ता पश्चात् प्राप्त निवेदन नम्बर प्रयोग गरी जुनसुकै समयमा शाखा प्रमाणीकरण र प्रगति स्थिति ट्र्याक गर्नुहोस्।</p>
            </div>
        </div>
    </div>
</div>
@endsection
