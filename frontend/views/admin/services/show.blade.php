@extends('layouts.admin', ['pageTitle' => 'सेवा विवरण'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> सेवा सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="info" class="me-2 text-primary"></i>सेवा सम्बन्धी जानकारी</h6>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-2">{{ $service->name }}</h5>
                <span class="badge bg-primary mb-3">{{ $service->department->name ?? 'तोकिएको छैन' }}</span>

                <p class="text-muted small mb-4">{{ $service->description ?? 'कुनै विवरण प्रदान गरिएको छैन।' }}</p>

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">दस्तुर:</span>
                        <span class="fw-bold text-success">रु. {{ number_format($service->fee, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">प्रशोधन समय:</span>
                        <span class="fw-semibold">{{ $service->processing_days }} दिन</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">स्थिति:</span>
                        @if($service->status)
                            <span class="badge-status badge-active">सक्रिय</span>
                        @else
                            <span class="badge-status badge-inactive">निष्क्रिय</span>
                        @endif
                    </li>
                </ul>

                <h6 class="fw-bold fs-7 text-uppercase text-muted mt-4 mb-2">आवश्यक कागजातहरू</h6>
                @if(!empty($service->required_documents) && is_array($service->required_documents))
                    <ul class="list-group list-group-flush">
                        @foreach($service->required_documents as $doc)
                            <li class="list-group-item px-0 py-1 small">
                                <i data-lucide="file-check" class="text-primary me-2"></i>{{ $doc }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted small">कुनै विशेष कागजात आवश्यक छैन।</span>
                @endif
            </div>
            <div class="card-footer bg-white text-end">
                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary">
                    <i data-lucide="pencil" class="me-1"></i> सेवा सम्पादन गर्नुहोस्
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card table-card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="file-text" class="me-2 text-primary"></i>यस सेवाका लागि प्राप्त आवेदनहरू</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>आवेदन नं.</th>
                            <th>निवेदक</th>
                            <th>स्थिति</th>
                            <th>पेश गरेको मिति</th>
                            <th>कार्य</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($service->applications as $app)
                            <tr>
                                <td class="fw-bold text-primary">{{ $app->application_number }}</td>
                                <td>{{ $app->applicant_name }}</td>
                                <td><span class="badge-status {{ $app->getStatusBadgeClass() }}">{{ $app->getStatusLabel() }}</span></td>
                                <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                        <i data-lucide="eye"></i> विवरण
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">यस सेवाका लागि हालसम्म कुनै आवेदन पेश गरिएको छैन।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
