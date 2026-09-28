@extends('layouts.admin', ['pageTitle' => 'सरकारी सेवाहरू व्यवस्थापन'])

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h5 class="mb-1 font-weight-bold">सरकारी सेवाहरू</h5>
        <span class="text-muted small">उपलब्ध सार्वजनिक सेवाहरू, दस्तुर र आवश्यकताहरू व्यवस्थापन गर्नुहोस्</span>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> नयाँ सेवा थप्नुहोस्
    </a>
</div>

<!-- Filters -->
<div class="card mb-4 p-3 bg-light border-0 shadow-sm">
    <form action="{{ route('admin.services.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control" placeholder="सेवाको नामद्वारा खोज्नुहोस्..." value="{{ request('search') }}">
        </div>
        <div class="col-12 col-md-4">
            <select name="department_id" class="form-select">
                <option value="">सबै विभागहरू</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-100"><i data-lucide="search" class="me-1"></i> खोज्नुहोस्</button>
            @if(request()->hasAny(['search', 'department_id']))
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary" title="पुनः सेट"><i data-lucide="x"></i></a>
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
                    <th>सेवाको नाम</th>
                    <th>मन्त्रालय / विभाग</th>
                    <th>दस्तुर</th>
                    <th>प्रशोधन समय</th>
                    <th>निवेदनहरू</th>
                    <th>स्थिति</th>
                    <th>कार्यहरू</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <a href="{{ route('admin.services.show', $service) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $service->name }}
                            </a>
                            @if(!empty($service->required_documents))
                                <div class="small text-muted"><i data-lucide="paperclip" class="me-1"></i>{{ count($service->required_documents) }} कागजात आवश्यक</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $service->department->name ?? 'तोकिएको छैन' }}</span>
                        </td>
                        <td class="fw-bold text-success">
                            रु. {{ number_format($service->fee, 2) }}
                        </td>
                        <td>
                            <span class="small"><i data-lucide="clock" class="me-1 text-muted"></i>{{ $service->processing_days }} दिन</span>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $service->applications_count }}</span>
                        </td>
                        <td>
                            @if($service->status)
                                <span class="badge-status badge-active">सक्रिय</span>
                            @else
                                <span class="badge-status badge-inactive">निष्क्रिय</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.services.show', $service) }}" class="btn btn-outline-primary" title="विवरण हेर्नुहोस्">
                                    <i data-lucide="eye"></i>
                                </a>
                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-secondary" title="सम्पादन">
                                    <i data-lucide="pencil"></i>
                                </a>
                                <form action="{{ route('admin.services.destroy', $service) }}" method="POST" onsubmit="return confirm('के तपाईं यो सेवा हटाउन चाहनुहुन्छ?');" class="d-inline">
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
                        <td colspan="8" class="text-center py-4 text-muted">कुनै पनि सेवा भेटिएन।</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($services->hasPages())
        <div class="card-footer bg-white">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
