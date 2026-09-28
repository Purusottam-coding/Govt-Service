@extends('layouts.admin', ['pageTitle' => 'दर्ता नागरिकहरू'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 font-weight-bold">नागरिक व्यवस्थापन</h5>
        <span class="text-muted small">दर्ता नागरिकहरू र उनीहरूको निवेदन इतिहास हेर्नुहोस्</span>
    </div>
</div>

<!-- Search -->
<div class="card mb-4 p-3 bg-light border-0 shadow-sm">
    <form action="{{ route('admin.citizens.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-9">
            <input type="text" name="search" class="form-control" placeholder="नागरिकको नाम, इमेल वा फोनद्वारा खोज्नुहोस्..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i data-lucide="search"></i> खोज्नुहोस्</button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.citizens.index') }}" class="btn btn-outline-secondary" title="पुनः सेट"><i data-lucide="x"></i></a>
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
                    <th>#</th>
                    <th>नागरिकको नाम</th>
                    <th>इमेल</th>
                    <th>फोन</th>
                    <th>दर्ता मिति</th>
                    <th>निवेदनहरू</th>
                    <th>कार्य</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citizens as $citizen)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($citizen->profile_photo)
                                    <img src="{{ asset('storage/' . $citizen->profile_photo) }}" alt="{{ $citizen->name }}" class="rounded-circle object-fit-cover" style="width:32px;height:32px;">
                                @else
                                    <div class="user-avatar" style="width:32px;height:32px;font-size:.8rem;">
                                        {{ strtoupper(substr($citizen->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="fw-bold text-dark">{{ $citizen->name }}</span>
                            </div>
                        </td>
                        <td>{{ $citizen->email }}</td>
                        <td>{{ $citizen->phone ?? 'N/A' }}</td>
                        <td>{{ $citizen->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                                {{ $citizen->applications_count }} निवेदन
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.citizens.show', $citizen) }}" class="btn btn-sm btn-outline-primary">
                                <i data-lucide="eye" class="me-1"></i> प्रोफाइल हेर्नुहोस्
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">खोज अनुसार कुनै नागरिक भेटिएन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($citizens->hasPages())
        <div class="card-footer bg-white">
            {{ $citizens->links() }}
        </div>
    @endif
</div>
@endsection
