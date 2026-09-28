@extends('layouts.admin', ['pageTitle' => 'नागरिक प्रतिक्रिया तथा गुनासाहरू'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 font-weight-bold">प्रतिक्रिया तथा सोधपुछहरू</h5>
        <span class="text-muted small">नागरिकको प्रतिक्रिया समीक्षा गरी आधिकारिक जवाफ प्रदान गर्नुहोस्</span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4 p-3 bg-light border-0 shadow-sm">
    <form action="{{ route('admin.feedback.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-9">
            <select name="status" class="form-select">
                <option value="">सबै स्थितिहरू</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>खुला / जवाफको प्रतीक्षामा</option>
                <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>जवाफ दिइएको</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>बन्द गरिएको</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i data-lucide="filter"></i> छान्नुहोस्</button>
            @if(request()->filled('status'))
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary" title="पुनः सेट"><i data-lucide="x"></i></a>
            @endif
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>विषय</th>
                    <th>नागरिक</th>
                    <th>सेवा</th>
                    <th>स्थिति</th>
                    <th>पेश मिति</th>
                    <th>कार्य</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbackList as $fb)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $fb->subject }}</div>
                            <div class="small text-muted text-truncate" style="max-width:300px;">{{ Str::limit($fb->message, 80) }}</div>
                        </td>
                        <td>
                            <div class="fw-medium">{{ $fb->user->name ?? 'नागरिक' }}</div>
                            <div class="small text-muted">{{ $fb->user->email ?? '' }}</div>
                        </td>
                        <td>{{ $fb->service->name ?? 'सामान्य सोधपुछ' }}</td>
                        <td><span class="badge-status {{ $fb->getStatusBadgeClass() }}">{{ ucfirst($fb->status) }}</span></td>
                        <td>{{ $fb->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.feedback.show', $fb) }}" class="btn btn-sm btn-outline-primary">
                                <i data-lucide="reply" class="me-1"></i> समीक्षा र जवाफ
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">कुनै पनि प्रतिक्रिया सन्देश भेटिएन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($feedbackList->hasPages())
        <div class="card-footer bg-white">
            {{ $feedbackList->links() }}
        </div>
    @endif
</div>
@endsection
