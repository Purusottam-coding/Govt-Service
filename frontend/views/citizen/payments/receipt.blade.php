@extends('layouts.citizen', ['pageTitle' => 'आधिकारिक भुक्तानी रसिद'])

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center no-print">
    <a href="{{ route('citizen.applications.show', $application) }}" class="btn btn-sm btn-outline-secondary">
        <i data-lucide="arrow-left" class="me-1"></i> निवेदन विवरणमा फर्कनुहोस्
    </a>
    <button onclick="window.print()" class="btn btn-sm btn-primary">
        <i data-lucide="printer" class="me-1"></i> रसिद प्रिन्ट गर्नुहोस्
    </button>
</div>

<div class="receipt shadow-sm">
    <div class="receipt-header">
        <div class="brand-icon-sm mx-auto mb-2" style="width:48px;height:48px;background:linear-gradient(135deg, var(--primary), var(--accent));border-radius:.75rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.2rem;">
            <i data-lucide="building-2"></i>
        </div>
        <h4 class="fw-bold mb-0">नेपाल सरकार — सरकारी सेवा प्रणाली</h4>
        <span class="text-muted small text-uppercase tracking-wider">आधिकारिक भुक्तानी रसिद (Payment Receipt)</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <span class="text-muted extra-small d-block">रसिद / कारोबार नम्बर</span>
            <span class="fw-bold text-dark">{{ $application->payment->transaction_id }}</span>
        </div>
        <div class="col-6 text-end">
            <span class="text-muted extra-small d-block">भुक्तानी मिति तथा समय</span>
            <span class="fw-bold text-dark">{{ $application->payment->paid_at ? $application->payment->paid_at->format('M d, Y h:i A') : now()->format('M d, Y') }}</span>
        </div>
    </div>

    <div class="bg-light p-3 rounded mb-4">
        <div class="row g-2 small">
            <div class="col-6 text-muted">निवेदकको नाम:</div>
            <div class="col-6 text-end fw-bold text-dark">{{ $application->applicant_name }}</div>

            <div class="col-6 text-muted">निवेदन नम्बर:</div>
            <div class="col-6 text-end fw-bold text-primary">{{ $application->application_number }}</div>

            <div class="col-6 text-muted">अनुरोध गरिएको सेवा:</div>
            <div class="col-6 text-end fw-semibold text-dark">{{ $application->service->name }}</div>

            <div class="col-6 text-muted">मन्त्रालय / विभाग:</div>
            <div class="col-6 text-end fw-semibold text-dark">{{ $application->service->department->name ?? 'नेपाल सरकार' }}</div>

            <div class="col-6 text-muted">भुक्तानीको माध्यम:</div>
            <div class="col-6 text-end fw-semibold text-uppercase">{{ $application->payment->payment_method }}</div>
        </div>
    </div>

    <table class="table table-bordered mb-4">
        <thead>
            <tr class="table-light">
                <th>विवरण</th>
                <th class="text-end">रकम</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>सरकारी प्रशोधन तथा दस्तुर शुल्क ({{ $application->service->name }})</td>
                <td class="text-end fw-bold">रु. {{ number_format($application->payment->amount, 2) }}</td>
            </tr>
            <tr class="fw-bold">
                <td class="text-end">कुल चुक्ता रकम:</td>
                <td class="text-end text-success fs-5">रु. {{ number_format($application->payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="text-center text-muted extra-small border-top pt-3">
        यो कम्प्युटरकृत रसिद हो, यसमा भौतिक हस्ताक्षर आवश्यक पर्दैन।<br>
        अनलाइन सरकारी सेवा प्रयोग गर्नुभएकोमा धन्यवाद।
    </div>
</div>
@endsection
