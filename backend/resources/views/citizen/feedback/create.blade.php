@extends('layouts.citizen', ['pageTitle' => 'गुनासो/सुझाव दर्ता गर्नुहोस्'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i data-lucide="message-square" class="me-2 text-primary"></i>गुनासो, समस्या वा सुझाव पेश फारम</h6>
                <a href="{{ route('citizen.feedback.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i data-lucide="arrow-left" class="me-1"></i> फर्कनुहोस्
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('citizen.feedback.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="service_id" class="form-label">संबंधित सरकारी सेवा (ऐच्छिक)</label>
                        <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror">
                            <option value="">साधारण सोधपुछ / सामान्य सुझाव</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}" {{ old('service_id') == $srv->id ? 'selected' : '' }}>
                                    {{ $srv->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">विषय <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="आफ्नो जिज्ञासा वा सुझावको विषय">
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label">विस्तृत सन्देश / समस्या विवरण <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required placeholder="आफ्नो प्रश्न, समस्या वा सुझाव विस्तृत रूपमा लेख्नुहोस्...">{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('citizen.feedback.index') }}" class="btn btn-light">रद्द गर्नुहोस्</a>
                        <button type="submit" class="btn btn-primary px-4"><i data-lucide="send" class="me-1"></i> दर्ता गर्नुहोस्</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
