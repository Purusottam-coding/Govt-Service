@extends('layouts.admin', ['pageTitle' => 'मन्त्रालय / विभाग व्यवस्थापन'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 font-weight-bold">सरकारी मन्त्रालय तथा विभागहरू</h5>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> नयाँ विभाग थप्नुहोस्
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>विभागको नाम</th>
                    <th>सम्पर्क जानकारी</th>
                    <th>सेवा संख्या</th>
                    <th>स्थिति</th>
                    <th>कार्यहरू</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $dept->name }}</div>
                            <div class="small text-muted text-truncate" style="max-width:300px;">{{ $dept->description ?? 'विवरण उपलब्ध छैन' }}</div>
                        </td>
                        <td>
                            <div class="small"><i data-lucide="telephone" class="me-1 text-muted"></i>{{ $dept->phone ?? 'N/A' }}</div>
                            <div class="small"><i data-lucide="mail" class="me-1 text-muted"></i>{{ $dept->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-semibold">{{ $dept->services_count }} सेवाहरू</span>
                        </td>
                        <td>
                            @if($dept->status)
                                <span class="badge-status badge-active">सक्रिय</span>
                            @else
                                <span class="badge-status badge-inactive">निष्क्रिय</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-outline-secondary" title="सम्पादन">
                                    <i data-lucide="pencil"></i>
                                </a>
                                <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST" onsubmit="return confirm('के तपाईं यो विभाग हटाउन चाहनुहुन्छ?');" class="d-inline">
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
                        <td colspan="6" class="text-center py-4 text-muted">हालसम्म कुनै विभाग थपिएको छैन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($departments->hasPages())
        <div class="card-footer bg-white">
            {{ $departments->links() }}
        </div>
    @endif
</div>
@endsection
