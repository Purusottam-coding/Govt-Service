@extends('layouts.admin', ['pageTitle' => 'प्रतिक्रिया विवरण'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.feedback.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> प्रतिक्रिया सूचीमा फर्कनुहोस्
    </a>
</div>

<div class="row g-4">
    <!-- Feedback Message Card -->
    <div class="col-12 col-lg-7">
        <div class="card mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="chat-left-quote" class="me-2 text-primary"></i>{{ $feedback->subject }}</h6>
                <span class="badge-status {{ $feedback->getStatusBadgeClass() }}">{{ $feedback->status == 'open' ? 'खुला' : ($feedback->status == 'replied' ? 'जवाफ दिइएको' : 'बन्द') }}</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between text-muted small mb-3 border-bottom pb-2">
                    <span><strong>पठाउने:</strong> {{ $feedback->user->name }} ({{ $feedback->user->email }})</span>
                    <span>{{ $feedback->created_at->format('M d, Y h:i A') }}</span>
                </div>

                @if($feedback->service)
                    <div class="mb-3">
                        <span class="badge bg-light text-dark border">सेवा: {{ $feedback->service->name }}</span>
                    </div>
                @endif

                <div class="bg-light p-3 rounded mb-4">
                     <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $feedback->message }}</p>
                </div>

                @if($feedback->admin_reply)
                    <div class="border-start border-3 border-primary ps-3 py-2 bg-primary-subtle rounded-end">
                        <h6 class="fw-bold text-primary mb-1"><i data-lucide="reply-fill" class="me-1"></i> आधिकारिक जवाफ:</h6>
                        <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $feedback->admin_reply }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reply Form Card -->
    <div class="col-12 col-lg-5">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="reply" class="me-2 text-primary"></i>जवाफ पठाउनुहोस्</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.feedback.reply', $feedback) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="admin_reply" class="form-label">जवाफ सन्देश <span class="text-danger">*</span></label>
                        <textarea name="admin_reply" id="admin_reply" rows="6" class="form-control @error('admin_reply') is-invalid @enderror" required placeholder="नागरिकको लागि आधिकारिक जवाफ लेख्नुहोस्...">{{ old('admin_reply', $feedback->admin_reply) }}</textarea>
                        @error('admin_reply')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">प्रतिक्रियाको स्थिति परिवर्तन <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="replied" {{ $feedback->status == 'replied' ? 'selected' : '' }}>जवाफ दिइएको भनेर चिह्नित गर्नुहोस्</option>
                            <option value="closed" {{ $feedback->status == 'closed' ? 'selected' : '' }}>बन्द गरिएको भनेर चिह्नित गर्नुहोस्</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i data-lucide="send" class="me-1"></i> जवाफ पेश गर्नुहोस्</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
