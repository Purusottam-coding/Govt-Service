@extends('layouts.admin', ['pageTitle' => 'प्रशासकीय ड्यासबोर्ड'])

@section('content')
<!-- Admin Welcome Banner with Barhadashi Municipality Building Background -->
<div class="card text-white mb-4 border-0 overflow-hidden shadow-sm barhadashi-dashboard-banner">
    <div class="barhadashi-banner-bg" style="background-image: url('{{ asset('images/barhadashi_building.jpg') }}');"></div>
    <div class="barhadashi-banner-overlay"></div>
    <div class="card-body p-4 p-lg-5 barhadashi-banner-content">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="barhadashi-badge-pill">
                        <i data-lucide="shield" style="width: 14px; height: 14px;"></i>
                        नेपाल सरकार • बाह्रदशी गाउँपालिका • प्रशासकीय कक्ष
                    </span>
                </div>
                <h2 class="fw-extrabold text-white mb-2" style="font-size: 1.75rem;">स्वागत छ, {{ auth()->user()->name }}!</h2>
                <p class="text-white-50 mb-0" style="color: rgba(255, 255, 255, 0.90) !important; max-width: 680px; font-size: 0.94rem; line-height: 1.55;">
                    गाउँ कार्यपालिकाको कार्यालय, झापा — नागरिक सेवा आवेदन, सिफारिस प्रमाणीकरण, डिजिटल राजस्व भुक्तानी तथा समग्र e-Governance कार्यसम्पादन स्थिति।
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 justify-content-lg-end">
                    <a href="{{ route('admin.applications.index') }}" class="btn btn-light fw-bold px-4 py-2.5 text-primary shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="file-check"></i>
                        <span>निवेदन समीक्षा गर्नुहोस्</span>
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-outline-light fw-semibold px-4 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                        <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                        <span>नयाँ सेवा दर्ता</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue & Analytics Quick Spotlight Banner -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-3 p-lg-4 mb-4 border-start border-4 border-success">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 rounded-circle bg-success-subtle text-success">
                <i data-lucide="wallet" style="width: 28px; height: 28px;"></i>
            </div>
            <div>
                <span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1 rounded-pill extra-small mb-1">सरकारी कोष • राजस्व विवरण</span>
                <h3 class="fw-extrabold text-dark mb-0 fs-3">रु. {{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                <small class="text-muted fw-semibold">हालसम्म नागरिक सेवाहरूबाट संकलित कुल प्रमाणित सरकारी राजस्व</small>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-primary fw-bold px-4 py-2.5 d-inline-flex align-items-center gap-2 shadow-sm">
                <i data-lucide="bar-chart-3" style="width: 18px; height: 18px;"></i>
                <span>शाखागत रिपोर्ट तथा एनालिटिक्स &rarr;</span>
            </a>
        </div>
    </div>
</div>

<!-- Primary Stat Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">कुल रेकर्ड</span>
                    <div class="stat-value text-dark fw-bold" style="font-size: 1.95rem;">{{ number_format($stats['total_applications']) }}</div>
                    <div class="stat-label text-muted fw-semibold">कुल प्राप्त निवेदनहरू</div>
                </div>
                <div class="stat-icon primary"><i data-lucide="file-text"></i></div>
            </div>
            <div class="pt-2 border-top extra-small text-muted d-flex align-items-center justify-content-between">
                <span>सम्पूर्ण शाखाहरू</span>
                <a href="{{ route('admin.applications.index') }}" class="text-primary text-decoration-none fw-semibold">सूची हेर्नुहोस् &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-semibold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">कारबाही आवश्यक</span>
                    <div class="stat-value text-dark fw-bold" style="font-size: 1.95rem;">{{ number_format($stats['pending_applications']) }}</div>
                    <div class="stat-label text-muted fw-semibold">छानबिन बाँकी (पेन्डिङ)</div>
                </div>
                <div class="stat-icon warning"><i data-lucide="clock"></i></div>
            </div>
            <div class="pt-2 border-top extra-small text-muted d-flex align-items-center justify-content-between">
                <span>प्रक्रिया पर्खिरहेका</span>
                <a href="{{ route('admin.applications.index') }}" class="text-warning text-decoration-none fw-semibold">समीक्षा &rarr;</a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-success-subtle text-success fw-semibold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">स्वीकृत सिफारिस</span>
                    <div class="stat-value text-dark fw-bold" style="font-size: 1.95rem;">{{ number_format($stats['approved_applications']) }}</div>
                    <div class="stat-label text-muted fw-semibold">स्वीकृत निवेदनहरू</div>
                </div>
                <div class="stat-icon success"><i data-lucide="check-circle-2"></i></div>
            </div>
            <div class="pt-2 border-top extra-small text-muted d-flex align-items-center justify-content-between">
                <span>सफल प्रमाणीकरण</span>
                <span class="text-success fw-semibold">पूर्ण</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-info-subtle text-info fw-semibold px-2 py-0.5 rounded-pill extra-small mb-1 d-inline-block">नागरिक लगत</span>
                    <div class="stat-value text-dark fw-bold" style="font-size: 1.95rem;">{{ number_format($stats['total_citizens']) }}</div>
                    <div class="stat-label text-muted fw-semibold">दर्ता नागरिकहरू</div>
                </div>
                <div class="stat-icon info"><i data-lucide="users"></i></div>
            </div>
            <div class="pt-2 border-top extra-small text-muted d-flex align-items-center justify-content-between">
                <span>सक्रिय सेवाग्राही</span>
                <a href="{{ route('admin.citizens.index') }}" class="text-info text-decoration-none fw-semibold">नागरिक नामावली &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Admin Shortcut Management Tiles -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.services.create') }}" class="admin-action-card">
            <div class="admin-action-icon bg-action-blue">
                <i data-lucide="plus-circle"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">नयाँ सेवा दर्ता</h6>
                <small class="text-muted">नागरिक सेवा थप्नुहोस्</small>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.notices.create') }}" class="admin-action-card">
            <div class="admin-action-icon bg-action-crimson">
                <i data-lucide="megaphone"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">सूचना प्रकाशन</h6>
                <small class="text-muted">सार्वजनिक सूचना जारी</small>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.qr-codes.index') }}" class="admin-action-card">
            <div class="admin-action-icon bg-action-gold">
                <i data-lucide="qr-code"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">भुक्तानी QR कोड</h6>
                <small class="text-muted">राजस्व खाता व्यवस्थापन</small>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admin.citizens.index') }}" class="admin-action-card">
            <div class="admin-action-icon bg-action-green">
                <i data-lucide="user-check"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark mb-0 fs-6">नागरिक लगत</h6>
                <small class="text-muted">प्रमाणित प्रोफाइलहरू</small>
            </div>
        </a>
    </div>
</div>

<!-- Secondary Metric Badges Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="admin-metric-card">
            <div class="rounded-circle p-2.5 bg-primary-subtle text-primary">
                <i data-lucide="building-2"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $stats['total_departments'] }}</h5>
                <span class="text-muted extra-small fw-semibold">शाखा / विभागहरू</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-metric-card">
            <div class="rounded-circle p-2.5 bg-success-subtle text-success">
                <i data-lucide="settings"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $stats['total_services'] }}</h5>
                <span class="text-muted extra-small fw-semibold">सक्रिय सरकारी सेवाहरू</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-metric-card">
            <div class="rounded-circle p-2.5 bg-info-subtle text-info">
                <i data-lucide="award"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $stats['completed_applications'] }}</h5>
                <span class="text-muted extra-small fw-semibold">सम्पन्न सेवा कार्यहरू</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="admin-metric-card">
            <div class="rounded-circle p-2.5 bg-danger-subtle text-danger">
                <i data-lucide="message-square"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $stats['pending_feedback'] }}</h5>
                <span class="text-muted extra-small fw-semibold">सम्बोधन बाँकी गुनासो</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Applications Table -->
    <div class="col-12 col-xl-8">
        <div class="card table-card h-100 shadow-sm border">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded-circle bg-primary-subtle text-primary">
                        <i data-lucide="file-text" style="width: 18px; height: 18px;"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">हालै प्राप्त निवेदनहरू</h6>
                        <span class="text-muted extra-small">नागरिकहरूबाट प्राप्त नयाँ आवेदन सूची</span>
                    </div>
                </div>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 rounded-pill">
                    सबै हेर्नुहोस् ({{ $stats['total_applications'] }})
                </a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">निवेदन नं.</th>
                            <th>निवेदक</th>
                            <th>सेवाको नाम</th>
                            <th>स्थिति</th>
                            <th>पेश मिति</th>
                            <th class="text-end pe-4">कार्य</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $app)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-primary font-monospace">#{{ $app->application_number }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="table-avatar">
                                            {{ strtoupper(substr($app->applicant_name ?? 'N', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $app->applicant_name }}</div>
                                            <div class="extra-small text-muted">{{ $app->applicant_email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark text-truncate d-inline-block" style="max-width: 170px;" title="{{ $app->service->name ?? 'N/A' }}">
                                        {{ $app->service->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status {{ $app->getStatusBadgeClass() }}">
                                        {{ $app->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-action btn-outline-primary">
                                        <i data-lucide="eye"></i> समीक्षा
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i data-lucide="folder-open" class="d-block mx-auto mb-2 text-muted" style="width: 36px; height: 36px; opacity: 0.5;"></i>
                                    हालसम्म कुनै पनि निवेदन प्राप्त भएको छैन।
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-3 px-4 d-flex justify-content-between align-items-center border-top">
                <span class="small text-muted fw-medium">हालैका {{ count($recentApplications) }} वटा निवेदन देखाइएको छ</span>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-sm btn-primary fw-semibold px-3 rounded-pill">
                    सबै प्राप्त निवेदनहरू व्यवस्थापन <i data-lucide="arrow-right" class="ms-1" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Feedback Widget -->
    <div class="col-12 col-xl-4">
        <div class="card h-100 shadow-sm border">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-2 rounded-circle bg-danger-subtle text-danger">
                        <i data-lucide="message-square" style="width: 18px; height: 18px;"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">नागरिक गुनासो / सुझाव</h6>
                        <span class="text-muted extra-small">सिधा नागरिक प्रतिक्रिया</span>
                    </div>
                </div>
                <a href="{{ route('admin.feedback.index') }}" class="btn btn-sm btn-outline-primary fw-semibold px-3 rounded-pill">
                    सबै हेर्नुहोस्
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentFeedback as $fb)
                        <a href="{{ route('admin.feedback.show', $fb) }}" class="list-group-item list-group-item-action p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 fw-semibold text-dark text-truncate" style="max-width: 200px;">{{ $fb->subject }}</h6>
                                <span class="badge-status {{ $fb->getStatusBadgeClass() }}">
                                    {{ $fb->status == 'open' ? 'दर्ता भएको' : ($fb->status == 'replied' ? 'जवाफ प्राप्त' : 'बन्द गरिएको') }}
                                </span>
                            </div>
                            <p class="small text-muted mb-2 text-truncate">{{ $fb->message }}</p>
                            <div class="d-flex justify-content-between align-items-center extra-small text-secondary fw-medium">
                                <span><i data-lucide="user" class="me-1" style="width: 12px; height: 12px;"></i>{{ $fb->user->name ?? 'नागरिक' }}</span>
                                <span>{{ $fb->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="p-5 text-center text-muted">
                            <i data-lucide="inbox" class="d-block mx-auto mb-2 text-muted" style="width: 36px; height: 36px; opacity: 0.5;"></i>
                            कुनै पनि गुनासो प्राप्त भएको छैन।
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer bg-light py-2.5 px-4 text-center border-top">
                <a href="{{ route('admin.feedback.index') }}" class="text-decoration-none small text-primary fw-semibold">
                    सबै नागरिक गुनासोहरूको जवाफ दिनुहोस् &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection