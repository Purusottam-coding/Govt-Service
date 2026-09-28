@extends('layouts.citizen', ['pageTitle' => $service->name])

@section('content')
<div class="mb-4">
    <a href="{{ route('citizen.services.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> सेवा सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold mb-2">
                    {{ $service->department->name ?? 'नेपाल सरकार' }}
                </span>
                <h3 class="fw-bold mb-3">{{ $service->name }}</h3>

                <p class="text-secondary leading-relaxed mb-4">{{ $service->description ?? 'यस सेवाको लागि विस्तृत विवरण थप गरिएको छैन।' }}</p>

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
                    <a href="{{ route('citizen.applications.create', ['service_id' => $service->id]) }}" class="btn btn-primary btn-lg px-4">
                        <i data-lucide="send" class="me-2"></i> अनलाइन आवेदन दिनुहोस्
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="info" class="me-2 text-primary"></i>सेवा विवरण संक्षिप्त</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 border-bottom pb-2">
                    <span class="text-muted small d-block">मन्त्रालय / विभाग</span>
                    <span class="fw-bold text-dark">{{ $service->department->name ?? 'N/A' }}</span>
                </div>
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
                    <span class="small d-block"><i data-lucide="telephone" class="me-1 text-muted"></i>{{ $service->department->phone ?? 'N/A' }}</span>
                    <span class="small d-block"><i data-lucide="mail" class="me-1 text-muted"></i>{{ $service->department->email ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
