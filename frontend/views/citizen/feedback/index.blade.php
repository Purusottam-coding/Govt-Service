@extends('layouts.citizen', ['pageTitle' => 'गुनासो तथा सुझाव'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">गुनासो तथा सुझाव हरू</h4>
        <span class="text-muted small">सरकारी सेवासम्बन्धी आफ्ना जिज्ञासा, समस्या वा सुझावहरू प्रशासकलाई पेश गर्नुहोस्</span>
    </div>
    <a href="{{ route('citizen.feedback.create') }}" class="btn btn-primary">
        <i data-lucide="plus" class="me-1"></i> गुनासो/सुझाव दर्ता गर्नुहोस्
    </a>
</div>

<div class="row g-4">
    <div class="col-12">
        @forelse($feedbackList as $fb)
            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $fb->subject }}</h6>
                            @if($fb->service)
                                <span class="badge bg-light text-dark border me-2">संबंधित सेवा: {{ $fb->service->name }}</span>
                            @endif
                            <span class="text-muted extra-small"><i data-lucide="calendar" class="me-1"></i>{{ $fb->created_at->format('M d, Y') }}</span>
                        </div>
                        <span class="badge-status {{ $fb->getStatusBadgeClass() }}">
                            {{ $fb->status == 'open' ? 'दर्ता भएको' : ($fb->status == 'replied' ? 'जवाफ प्राप्त' : 'बन्द गरिएको') }}
                        </span>
                    </div>

                    <p class="text-secondary small mb-3" style="white-space: pre-line;">{{ $fb->message }}</p>

                    @if($fb->admin_reply)
                        <div class="p-3 bg-light rounded border-start border-3 border-primary">
                            <div class="fw-bold text-primary small mb-1"><i data-lucide="reply-fill" class="me-1"></i> प्रशासकीय उत्तर:</div>
                            <p class="mb-0 small text-dark" style="white-space: pre-line;">{{ $fb->admin_reply }}</p>
                        </div>
                    @else
                        <div class="text-muted extra-small"><i data-lucide="clock" class="me-1"></i>आधिकारिक प्रशासकीय जवाफको पर्खाइमा।</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card text-center py-5">
                <i data-lucide="chat-left-dots" class="text-muted fs-1 mb-2 d-block"></i>
                <h6 class="fw-bold text-muted">हालसम्म कुनै पनि गुनासो वा सुझाव दर्ता भएको छैन।</h6>
                <p class="small text-muted mb-3">केही समस्या वा सुझाव छ? हामीलाई पठाउनुहोस्।</p>
                <div>
                    <a href="{{ route('citizen.feedback.create') }}" class="btn btn-sm btn-primary">गुनासो/सुझाव दर्ता गर्नुहोस्</a>
                </div>
            </div>
        @endforelse

        @if($feedbackList->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $feedbackList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
