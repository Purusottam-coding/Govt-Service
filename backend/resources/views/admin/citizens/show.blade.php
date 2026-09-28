@extends('layouts.admin', ['pageTitle' => 'नागरिक प्रोफाइल'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.citizens.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> नागरिक सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row g-4">
    <!-- Citizen Profile Card -->
    <div class="col-12 col-lg-4">
        <div class="card text-center p-4">
            @if($citizen->profile_photo)
                <img src="{{ asset('storage/' . $citizen->profile_photo) }}" alt="{{ $citizen->name }}" class="rounded-circle object-fit-cover mx-auto mb-3 border border-2 border-primary" style="width:80px;height:80px;">
            @else
                <div class="user-avatar mx-auto mb-3" style="width:72px;height:72px;font-size:1.75rem;">
                    {{ strtoupper(substr($citizen->name, 0, 1)) }}
                </div>
            @endif
            <h5 class="fw-bold mb-1">{{ $citizen->name }}</h5>
            <span class="badge bg-secondary mb-3">नागरिक</span>

            <div class="text-start mt-3">
                <div class="mb-2"><i data-lucide="mail" class="me-2 text-muted"></i><strong>इमेल:</strong> {{ $citizen->email }}</div>
                <div class="mb-2"><i data-lucide="telephone" class="me-2 text-muted"></i><strong>फोन:</strong> {{ $citizen->phone ?? 'N/A' }}</div>
                <div class="mb-2"><i data-lucide="map-pin" class="me-2 text-muted"></i><strong>ठेगाना:</strong> {{ $citizen->address ?? 'N/A' }}</div>
                <div class="mb-0"><i data-lucide="calendar" class="me-2 text-muted"></i><strong>दर्ता मिति:</strong> {{ $citizen->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Application History -->
    <div class="col-12 col-lg-8">
        <div class="card table-card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="history" class="me-2 text-primary"></i>निवेदन इतिहास</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>निवेदन नं.</th>
                            <th>सेवा</th>
                            <th>स्थिति</th>
                            <th>पेश मिति</th>
                            <th>कार्य</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($citizen->applications as $app)
                            <tr>
                                <td class="fw-bold text-primary">{{ $app->application_number }}</td>
                                <td>{{ $app->service->name ?? 'N/A' }}</td>
                                <td><span class="badge-status {{ $app->getStatusBadgeClass() }}">{{ $app->getStatusLabel() }}</span></td>
                                <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                        <i data-lucide="eye"></i> हेर्नुहोस्
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">यस नागरिकद्वारा हालसम्म कुनै निवेदन पेश गरिएको छैन।</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
