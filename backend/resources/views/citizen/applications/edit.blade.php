@extends('layouts.citizen', ['pageTitle' => 'निवेदन सम्पादन'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i data-lucide="edit" class="me-2 text-primary"></i>निवेदन सम्पादन गर्नुहोस् (#{{ $application->application_number }})</h5>
                <a href="{{ route('citizen.applications.show', $application) }}" class="btn btn-sm btn-outline-secondary">
                    <i data-lucide="arrow-left" class="me-1"></i> फर्कनुहोस्
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('citizen.applications.update', $application) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Select Service -->
                    <div class="mb-4">
                        <label for="service_id" class="form-label fw-bold">सरकारी सेवा चयन गर्नुहोस् <span class="text-danger">*</span></label>
                        <select name="service_id" id="service_id" class="form-select form-select-lg @error('service_id') is-invalid @enderror" required onchange="updateServiceInfo(this)">
                            <option value="">-- सेवा चयन गर्नुहोस् --</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}"
                                    data-fee="{{ $srv->fee }}"
                                    data-days="{{ $srv->processing_days }}"
                                    data-docs="{{ is_array($srv->required_documents) ? implode(', ', $srv->required_documents) : '' }}"
                                    {{ old('service_id', $application->service_id) == $srv->id ? 'selected' : '' }}>
                                    {{ $srv->name }} ({{ $srv->department->name ?? 'नेपाल सरकार' }}) — {{ $srv->fee > 0 ? 'रु. ' . number_format($srv->fee, 2) : 'निःशुल्क' }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dynamic Service Info Alert -->
                    <div id="serviceInfoAlert" class="alert alert-info d-none mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>अनुमानित प्रशोधन समय:</strong> <span id="infoDays">-</span> दिन<br>
                                <strong>सरकारी दस्तुर:</strong> रु. <span id="infoFee">0.00</span>
                            </div>
                            <i data-lucide="info" class="fs-3"></i>
                        </div>
                        <div class="mt-2 small border-top pt-2" id="infoDocsWrapper">
                            <strong>आवश्यक कागजातहरू:</strong> <span id="infoDocs">कुनै पनि छैन</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i data-lucide="user" class="me-2 text-primary"></i>निवेदकको विवरण</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="applicant_name" class="form-label">पूरा नाम <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('applicant_name') is-invalid @enderror" id="applicant_name" name="applicant_name" value="{{ old('applicant_name', $application->applicant_name) }}" required>
                            @error('applicant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="applicant_email" class="form-label">इमेल ठेगाना <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('applicant_email') is-invalid @enderror" id="applicant_email" name="applicant_email" value="{{ old('applicant_email', $application->applicant_email) }}" required>
                            @error('applicant_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="applicant_phone" class="form-label">फोन नम्बर</label>
                            <input type="text" class="form-control @error('applicant_phone') is-invalid @enderror" id="applicant_phone" name="applicant_phone" value="{{ old('applicant_phone', $application->applicant_phone) }}" placeholder="+९७७ ९८००००००००">
                            @error('applicant_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="applicant_address" class="form-label">स्थायी ठेगाना</label>
                            <input type="text" class="form-control @error('applicant_address') is-invalid @enderror" id="applicant_address" name="applicant_address" value="{{ old('applicant_address', $application->applicant_address) }}" placeholder="जिल्ला, नगरपालिका/गाउँपालिका, वडा नं.">
                            @error('applicant_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Existing Uploaded Documents -->
                    @if($application->documents->count() > 0)
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i data-lucide="file-check" class="me-2 text-primary"></i>हाल अपलोड गरिएका कागजातहरू</h6>
                        <p class="text-muted small mb-2">हटाउन चाहनुहुने कागजातको अगाडि चिन्ह (चेक) लगाउनुहोस्:</p>
                        <div class="list-group mb-4">
                            @foreach($application->documents as $doc)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i data-lucide="file-text" class="text-primary"></i>
                                        <span class="fw-semibold small">{{ $doc->document_name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2">
                                            <i data-lucide="eye" class="me-1"></i> हेर्नुहोस्
                                        </a>
                                        <div class="form-check text-danger mb-0">
                                            <input class="form-check-input border-danger" type="checkbox" name="delete_documents[]" value="{{ $doc->id }}" id="del_doc_{{ $doc->id }}">
                                            <label class="form-check-label small text-danger fw-semibold" for="del_doc_{{ $doc->id }}">
                                                हटाउनुहोस्
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add New Documents -->
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i data-lucide="cloud-upload" class="me-2 text-primary"></i>थप कागजात अपलोड गर्नुहोस्</h6>
                    <p class="text-muted small mb-3">आवश्यक भएमा थप कागजातहरू **PDF** वा इमेज (JPG, PNG) ढाँचामा अपलोड गर्नुहोस्।</p>

                    <div id="documentsContainer">
                        <div class="row g-2 mb-3 document-row">
                            <div class="col-md-5">
                                <input type="text" name="document_names[]" class="form-control" placeholder="कागजातको नाम">
                            </div>
                            <div class="col-md-7">
                                <input type="file" name="documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted extra-small">PDF, JPG, PNG वा DOC (अधिकतम 5MB)</small>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-secondary mb-4" onclick="addDocumentRow()">
                        <i data-lucide="plus-circle" class="me-1"></i> थप कागजात थप्नुहोस्
                    </button>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('citizen.applications.show', $application) }}" class="btn btn-light">रद्द गर्नुहोस्</a>
                        <button type="submit" class="btn btn-primary px-4"><i data-lucide="save" class="me-1"></i> परिवर्तन सुरक्षित गर्नुहोस्</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateServiceInfo(select) {
        const option = select.options[select.selectedIndex];
        const alert = document.getElementById('serviceInfoAlert');
        if (option.value) {
            document.getElementById('infoFee').innerText = parseFloat(option.getAttribute('data-fee')).toFixed(2);
            document.getElementById('infoDays').innerText = option.getAttribute('data-days');
            const docs = option.getAttribute('data-docs');
            document.getElementById('infoDocs').innerText = docs || 'कुनै पनि आवश्यक छैन';
            alert.classList.remove('d-none');
        } else {
            alert.classList.add('d-none');
        }
    }

    function addDocumentRow() {
        const container = document.getElementById('documentsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'row g-2 mb-3 document-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="document_names[]" class="form-control" placeholder="कागजातको नाम">
            </div>
            <div class="col-md-6">
                <input type="file" name="documents[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger w-100" onclick="this.closest('.document-row').remove()"><i data-lucide="trash-2"></i></button>
            </div>
        `;
        container.appendChild(newRow);
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('service_id');
        if (select && select.value) updateServiceInfo(select);
    });
</script>
@endpush
@endsection
