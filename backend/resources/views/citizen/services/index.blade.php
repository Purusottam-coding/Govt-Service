@extends('layouts.citizen', ['pageTitle' => 'सरकारी सेवाहरू'])

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">सरकारी सेवाहरूको सूची</h4>
        <span class="text-muted small">उपलब्ध सार्वजनिक सेवाहरू खोज्नुहोस्, दस्तुर तथा आवश्यक कागजात हेर्नुहोस् र अनलाइन आवेदन दिनुहोस्</span>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="card mb-4 p-3 bg-white border-0 shadow-sm">
    <form action="{{ route('citizen.services.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i data-lucide="search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="सेवाको नाम वा शब्दद्वारा खोज्नुहोस्..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <select name="department_id" class="form-select">
                <option value="">सबै मन्त्रालय / विभागहरू</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100">खोज्नुहोस्</button>
            @if(request()->hasAny(['search', 'department_id']))
                <a href="{{ route('citizen.services.index') }}" class="btn btn-outline-secondary" title="पुनः सेट गर्नुहोस्"><i data-lucide="x"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Service Cards Grid -->
<div class="row g-4 mb-4">
    @forelse($services as $service)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="service-card">
                <span class="service-dept">{{ $service->department->name ?? 'नेपाल सरकार' }}</span>
                <h5 class="fw-bold mb-2 text-dark">{{ $service->name }}</h5>
                <p class="small text-muted mb-3 flex-grow-1">{{ Str::limit($service->description, 100) }}</p>

                <div class="mb-3 small text-secondary">
                    <div><i data-lucide="clock" class="me-1"></i><strong>अनुमानित समय:</strong> {{ $service->processing_days }} दिन</div>
                    @if(!empty($service->required_documents))
                        <div><i data-lucide="paperclip" class="me-1"></i><strong>आवश्यक कागजात:</strong> {{ count($service->required_documents) }} वटा</div>
                    @endif
                </div>

                <div class="service-meta">
                    <span class="service-fee">{{ $service->fee > 0 ? 'रु. ' . number_format($service->fee, 2) : 'निःशुल्क' }}</span>
                    <a href="{{ route('citizen.services.show', $service) }}" class="btn btn-sm btn-primary">
                        हेर्नुहोस् र आवेदन दिनुहोस् <i data-lucide="arrow-right" class="ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="text-center py-5 bg-white rounded border">
                <i data-lucide="search" class="text-muted fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold text-muted">कुनै पनि सरकारी सेवा भेटिएन।</h6>
                <p class="small text-muted">कृपया अर्को शब्द वा विभाग चयन गरी पुनः खोज्नुहोस्।</p>
            </div>
        </div>
    @endforelse
</div>

@if($services->hasPages())
    <div class="d-flex justify-content-center">
        {{ $services->links() }}
    </div>
@endif
@endsection
