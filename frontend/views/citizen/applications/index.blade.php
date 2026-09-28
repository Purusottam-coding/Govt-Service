@extends('layouts.citizen', ['pageTitle' => 'मेरा निवेदनहरू'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">मेरा पेश गरिएका निवेदनहरू</h4>
        <span class="text-muted small">तपाईंले पेश गर्नुभएका सबै हालैका तथा पुराना सरकारी निवेदनहरू हेर्नुहोस्</span>
    </div>
    <a href="{{ route('citizen.applications.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> नयाँ निवेदन पेश गर्नुहोस्
    </a>
</div>

<!-- Filters -->
<div class="card mb-4 p-3 bg-white border-0 shadow-sm">
    <form action="{{ route('citizen.applications.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-9">
            <select name="status" class="form-select">
                <option value="">सबै निवेदन स्थितिहरू</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>पेश गरिएको (पेन्डिङ)</option>
                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>छानबिनमा (Under Review)</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>स्वीकृत (Approved)</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>अस्वीकृत (Rejected)</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>सम्पन्न (Completed)</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i data-lucide="filter"></i> फिल्टर गर्नुहोस्</button>
            @if(request()->filled('status'))
                <a href="{{ route('citizen.applications.index') }}" class="btn btn-outline-secondary" title="पुनः सेट गर्नुहोस्"><i data-lucide="x"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>निवेदन नं.</th>
                    <th>सेवाको नाम</th>
                    <th>मन्त्रालय / विभाग</th>
                    <th>दस्तुर स्थिति</th>
                    <th>निवेदन स्थिति</th>
                    <th>पेश गरेको मिति</th>
                    <th>कार्य</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td class="fw-bold text-primary">{{ $app->application_number }}</td>
                        <td class="fw-semibold">{{ $app->service->name ?? 'N/A' }}</td>
                        <td>{{ $app->service->department->name ?? 'N/A' }}</td>
                        <td>
                            @if($app->payment)
                                @if($app->payment->status === 'pending')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">प्रमाणीकरणमा</span>
                                @elseif($app->payment->status === 'completed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">चुक्ता भएको</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold">असफल</span>
                                @endif
                            @elseif(($app->service->fee ?? 0) > 0)
                                <a href="{{ route('citizen.payments.create', $app) }}" class="badge bg-secondary-subtle text-secondary border border-secondary-subtle text-decoration-none fw-semibold">
                                    भुक्तानी गर्नुहोस् (रु. {{ number_format($app->service->fee, 2) }})
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">निःशुल्क</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status {{ $app->getStatusBadgeClass() }}">{{ $app->getStatusLabel() }}</span>
                        </td>
                        <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('citizen.applications.show', $app) }}" class="btn btn-sm btn-outline-primary" title="विवरण हेर्नुहोस्">
                                    <i data-lucide="eye" class="me-1"></i> विवरण
                                </a>
                                @if($app->canBeEdited())
                                    <a href="{{ route('citizen.applications.edit', $app) }}" class="btn btn-sm btn-outline-warning" title="सम्पादन गर्नुहोस्">
                                        <i data-lucide="pencil" class="me-1"></i> सम्पादन
                                    </a>
                                @endif
                                @if($app->canBeDeleted())
                                    <form action="{{ route('citizen.applications.destroy', $app) }}" method="POST" class="d-inline" onsubmit="return confirm('के तपाईं पक्का यो निवेदन हटाउन चाहनुहुन्छ? यो कार्य फिर्ता गर्न सकिँदैन।');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="हटाउनुहोस्">
                                            <i data-lucide="trash-2" class="me-1"></i> हटाउनुहोस्
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i data-lucide="folder-x" class="fs-2 d-block mb-2 text-muted"></i>
                            कुनै पनि निवेदन भेटिएन।<br>
                            <a href="{{ route('citizen.applications.create') }}" class="btn btn-sm btn-primary mt-2">नयाँ निवेदन पेश गर्नुहोस्</a>
                        </td>
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
