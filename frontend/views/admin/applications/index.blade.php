@extends('layouts.admin', ['pageTitle' => 'प्राप्त निवेदनहरू व्यवस्थापन'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 font-weight-bold">नागरिक निवेदनहरू</h5>
        <span class="text-muted small">पेश गरिएका निवेदनहरूको समीक्षा, ट्र्याकिङ र स्थिति अद्यावधिक गर्नुहोस्</span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4 p-3 bg-light border-0 shadow-sm">
    <form action="{{ route('admin.applications.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control" placeholder="निवेदन नं., नाम वा इमेलद्वारा खोज्नुहोस्..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3">
            <select name="status" class="form-select">
                <option value="">सबै स्थितिहरू</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>पेश गरिएको (पेन्डिङ)</option>
                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>छानबिनमा</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>स्वीकृत</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>अस्वीकृत</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>सम्पन्न</option>
            </select>
        </div>
        <div class="col-12 col-md-3">
            <select name="service_id" class="form-select">
                <option value="">सबै सेवाहरू</option>
                @foreach($services as $srv)
                    <option value="{{ $srv->id }}" {{ request('service_id') == $srv->id ? 'selected' : '' }}>
                        {{ $srv->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i data-lucide="search"></i> खोज्नुहोस्</button>
            @if(request()->hasAny(['search', 'status', 'service_id']))
                <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-secondary" title="पुनः सेट"><i data-lucide="x"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Table -->
<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>निवेदन नं.</th>
                    <th>निवेदक</th>
                    <th>सेवा / विभाग</th>
                    <th>दस्तुर</th>
                    <th>स्थिति</th>
                    <th>पेश गरेको मिति</th>
                    <th>कार्य</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td class="fw-bold text-primary">{{ $app->application_number }}</td>
                        <td>
                            <div class="fw-semibold">{{ $app->applicant_name }}</div>
                            <div class="small text-muted">{{ $app->applicant_email }}</div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $app->service->name ?? 'N/A' }}</div>
                            <div class="small text-muted">{{ $app->service->department->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            @if($app->payment)
                                @if($app->payment->status === 'pending')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                        <i data-lucide="clock-3" class="me-1"></i>प्रमाणीकरणमा (रु. {{ number_format($app->payment->amount, 2) }})
                                    </span>
                                @elseif($app->payment->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                                        <i data-lucide="check-circle" class="me-1"></i>चुक्ता (रु. {{ number_format($app->payment->amount, 2) }})
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">
                                        <i data-lucide="x-circle" class="me-1"></i>असफल
                                    </span>
                                @endif
                            @elseif(($app->service->fee ?? 0) > 0)
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold">
                                    <i data-lucide="clock" class="me-1"></i>बाँकी (रु. {{ number_format($app->service->fee, 2) }})
                                </span>
                            @else
                                <span class="badge bg-light text-muted border">निःशुल्क</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ $app->getStatusBadgeClass() }}">
                                {{ $app->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="small">{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</div>
                            <div class="small text-muted">{{ $app->submitted_at ? $app->submitted_at->format('h:i A') : '' }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-action btn-outline-primary">
                                <i data-lucide="eye" class="me-1"></i> समीक्षा
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">कुनै पनि निवेदन भेटिएन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="card-footer bg-white">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
