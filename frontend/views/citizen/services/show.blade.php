@extends('layouts.citizen', ['pageTitle' => $service->name])

@section('content')
@php
    $branchController = new \App\Http\Controllers\Citizen\BranchController();
    $branchMeta = $service->department ? $branchController->getBranchMetadata($service->department) : null;
@endphp

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="{{ route('citizen.services.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> सेवा सूचीमा फर्कनुहोस्
    </a>
    @if($service->department)
        <a href="{{ route('citizen.branches.show', $service->department) }}" class="btn btn-sm btn-outline-primary">
            <i data-lucide="building-2" class="me-1"></i> {{ $service->department->name }} (शाखा निर्देशिका)
        </a>
    @endif
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                        {{ $service->department->name ?? 'नेपाल सरकार' }}
                    </span>
                    @if($branchMeta)
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace">
                            <i data-lucide="map-pin" class="me-1 text-danger" style="width:12px;height:12px;"></i>{{ $branchMeta['room'] }}
                        </span>
                    @endif
                </div>

                <h3 class="fw-bold mb-3">{{ $service->name }}</h3>
                <p class="text-secondary leading-relaxed mb-4">{{ $service->description ?? 'यस सेवाको लागि विस्तृत विवरण थप गरिएको छैन।' }}</p>

                <!-- Branch Step Guidance Box -->
                @if($branchMeta)
                    <div class="p-4 bg-light rounded-4 border mb-4">
                        <h6 class="fw-bold text-dark mb-3">
                            <i data-lucide="map-pin" class="me-2 text-primary"></i>कुन शाखा जाने? के के गर्ने? (Branch & Procedure Workflow)
                        </h6>
                        <div class="row g-3">
                            @foreach($branchMeta['steps'] as $st)
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-white rounded-3 border h-100 shadow-sm">
                                        <div class="fw-bold text-primary mb-1 small d-flex align-items-center gap-2">
                                            <span class="badge bg-primary text-white rounded-circle" style="width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:0.75rem;">{{ $st['step'] }}</span>
                                            {{ $st['title'] }}
                                        </div>
                                        <p class="extra-small text-secondary mb-0">{{ $st['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <h6 class="fw-bold text-dark mb-3"><i data-lucide="file-text" class="me-2 text-primary"></i>आवेदनका लागि आवश्यक कागजातहरू</h6>
                @if(!empty($service->required_documents) && is_array($service->required_documents))
                    <ul class="list-group mb-4">
                        @foreach($service->required_documents as $doc)
                            <li class="list-group-item d-flex align-items-center gap-2 py-3">
                                <i data-lucide="check-circle-2" class="text-success fs-5"></i>
                                <div>
                                    <span class="fw-semibold text-dark d-block">{{ $doc }}</span>
                                    <span class="text-muted extra-small">स्क्यान गरिएको प्रतिलिपि तयार पार्नुहोस् (PDF, JPG, PNG)</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-light border mb-4">यस सेवाको लागि विशेष कागजात आवश्यक छैन।</div>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="{{ route('citizen.applications.create', ['service_id' => $service->id]) }}" class="btn btn-primary btn-lg px-4 fw-bold">
                        <i data-lucide="send" class="me-2"></i> अनलाइन आवेदन दिनुहोस्
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="info" class="me-2 text-primary"></i>सेवा तथा शाखा विवरण</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 border-bottom pb-2">
                    <span class="text-muted small d-block">मन्त्रालय / विषयगत शाखा</span>
                    <span class="fw-bold text-dark">{{ $service->department->name ?? 'N/A' }}</span>
                </div>
                @if($branchMeta)
                    <div class="mb-3 border-bottom pb-2">
                        <span class="text-muted small d-block">शाखा कोठा नं. / स्थान</span>
                        <span class="fw-bold text-primary">{{ $branchMeta['room'] }}</span>
                    </div>
                    <div class="mb-3 border-bottom pb-2">
                        <span class="text-muted small d-block">शाखा प्रमुख / अधिकृत</span>
                        <span class="fw-semibold text-dark small">{{ $branchMeta['officer'] }}</span>
                    </div>
                @endif
                <div class="mb-3 border-bottom pb-2">
                    <span class="text-muted small d-block">सरकारी दस्तुर</span>
                    <span class="fw-bold text-success fs-5">{{ $service->fee > 0 ? 'रु. ' . number_format($service->fee, 2) : 'निःशुल्क' }}</span>
                </div>
                <div class="mb-3 border-bottom pb-2">
                    <span class="text-muted small d-block">अनुमानित प्रशोधन समय</span>
                    <span class="fw-semibold text-dark"><i data-lucide="clock" class="me-1 text-muted"></i>{{ $service->processing_days }} कार्यदिन</span>
                </div>
                <div class="mb-0">
                    <span class="text-muted small d-block">विभाग सम्पर्क</span>
                    <span class="small d-block"><i data-lucide="phone" class="me-1 text-muted"></i>{{ $service->department->phone ?? 'N/A' }}</span>
                    <span class="small d-block"><i data-lucide="mail" class="me-1 text-muted"></i>{{ $service->department->email ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
