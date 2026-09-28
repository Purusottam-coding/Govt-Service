@extends('layouts.admin', ['pageTitle' => 'सूचना व्यवस्थापन'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 font-weight-bold">सार्वजनिक सूचना तथा घोषणाहरू</h5>
        <span class="text-muted small">सार्वजनिक पोर्टलमा प्रदर्शन हुने घोषणाहरू प्रकाशित गर्नुहोस्</span>
    </div>
    <a href="{{ route('admin.notices.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> नयाँ सूचना थप्नुहोस्
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>सूचनाको शीर्षक</th>
                    <th>प्रकाशन मिति</th>
                    <th>स्थिति</th>
                    <th>कार्यहरू</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notices as $notice)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $notice->title }}</div>
                            <div class="small text-muted text-truncate" style="max-width:400px;">{{ Str::limit($notice->content, 100) }}</div>
                        </td>
                        <td>
                            <div class="small"><i data-lucide="calendar" class="me-1 text-muted"></i>{{ $notice->published_at ? $notice->published_at->format('M d, Y') : 'मस्यौदा (Draft)' }}</div>
                        </td>
                        <td>
                            @if($notice->is_active)
                                <span class="badge-status badge-active">प्रकाशित</span>
                            @else
                                <span class="badge-status badge-inactive">निष्क्रिय</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.notices.edit', $notice) }}" class="btn btn-outline-secondary" title="सम्पादन">
                                    <i data-lucide="pencil"></i>
                                </a>
                                <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" onsubmit="return confirm('के तपाईं यो सूचना हटाउन चाहनुहुन्छ?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="हटाउनुहोस्">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">हालसम्म कुनै पनि सूचना प्रकाशित गरिएको छैन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($notices->hasPages())
        <div class="card-footer bg-white">
            {{ $notices->links() }}
        </div>
    @endif
</div>
@endsection
