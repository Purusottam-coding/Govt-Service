@extends('layouts.citizen', ['pageTitle' => 'नागरिक ड्यासबोर्ड'])

@section('content')
@php
    $hour = (int) date('H');
    if ($hour >= 5 && $hour < 12) {
        $greeting = 'शुभ प्रभात';
        $greetingIcon = 'sun-medium';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'शुभ दिन';
        $greetingIcon = 'sun';
    } else {
        $greeting = 'शुभ सन्ध्या';
        $greetingIcon = 'moon-star';
    }
@endphp

<!-- Hero Welcome Banner with Barhadashi Municipality Building Background -->
<div class="card text-white mb-4 border-0 shadow-sm barhadashi-dashboard-banner">
    <div class="barhadashi-banner-bg" style="background-image: url('{{ asset('images/barhadashi_building.jpg') }}');"></div>
    <div class="barhadashi-banner-overlay"></div>
    <div class="card-body p-4 p-lg-5 barhadashi-banner-content">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="greeting-time-badge">
                        <i data-lucide="{{ $greetingIcon }}" style="width: 14px; height: 14px;"></i> {{ $greeting }}
                    </span>
                    <span class="verified-citizen-badge">
                        <i data-lucide="shield-check" style="width: 14px; height: 14px;"></i> प्रमाणित नागरिक
                    </span>
                    <span class="barhadashi-badge-pill">बाह्रदशी गाउँपालिका • नागरिक सेवा पोर्टल</span>
                </div>
                <h2 class="fw-extrabold text-white mb-2" style="font-size: 2rem; letter-spacing: -0.02em;">
                    स्वागत छ, {{ auth()->user()->name }}!
                </h2>
                <p class="mb-0 text-white-50" style="color: rgba(255, 255, 255, 0.92) !important; font-size: 0.95rem; line-height: 1.6;">
                    गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा — घरमै बसेर सरकारी सेवाहरूमा अनलाइन आवेदन दिनुहोस्, दस्तुर भुक्तानी गर्नुहोस् र निवेदनको स्थिति प्रत्यक्ष ट्र्याक गर्नुहोस्।
                </p>
            </div>
            
            <!-- Quick Service Search Bar -->
            <div class="col-12 col-lg-5">
                <form action="{{ route('citizen.services.index') }}" method="GET">
                    <label class="text-white-50 small fw-semibold mb-2 d-flex align-items-center gap-1">
                        <i data-lucide="search" style="width: 13px; height: 13px;"></i> अनलाइन सेवा द्रुत खोजी:
                    </label>
                    <div class="banner-search-box">
                        <i data-lucide="search" class="banner-search-icon"></i>
                        <input type="text" 
                               name="search" 
                               class="banner-search-input" 
                               placeholder="कुन सेवा खोज्दै हुनुहुन्छ? (उदा. नागरिकता, जन्म दर्ता...)" 
                               autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary px-3 py-2 fw-semibold text-nowrap rounded-3">
                            खोज्नुहोस्
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts Grid (४ द्रुत सरकारी सेवाहरू) -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <a href="{{ route('citizen.services.index') }}" class="quick-action-card">
            <div class="quick-action-icon-box action-blue">
                <i data-lucide="file-plus-2"></i>
            </div>
            <div>
                <h6 class="quick-action-title">नयाँ आवेदन</h6>
                <p class="quick-action-desc">सरकारी सेवा छनौट गर्नुहोस्</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('citizen.applications.index') }}" class="quick-action-card">
            <div class="quick-action-icon-box action-green">
                <i data-lucide="search-check"></i>
            </div>
            <div>
                <h6 class="quick-action-title">निवेदन ट्र्याकिङ</h6>
                <p class="quick-action-desc">पेश गरिएका निवेदनको स्थिति</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('citizen.services.index') }}" class="quick-action-card">
            <div class="quick-action-icon-box action-amber">
                <i data-lucide="receipt"></i>
            </div>
            <div>
                <h6 class="quick-action-title">दस्तुर भुक्तानी</h6>
                <p class="quick-action-desc">QR कोड तथा रसिद विवरण</p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('citizen.feedback.index') }}" class="quick-action-card">
            <div class="quick-action-icon-box action-purple">
                <i data-lucide="message-square-plus"></i>
            </div>
            <div>
                <h6 class="quick-action-title">गुनासो तथा सुझाव</h6>
                <p class="quick-action-desc">गाउँपालिकालाई प्रत्यक्ष राय</p>
            </div>
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-primary">{{ $stats['total_applications'] }}</div>
                    <div class="stat-label">कुल निवेदनहरू</div>
                </div>
                <div class="stat-icon primary"><i data-lucide="file-text"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-warning">{{ $stats['pending_applications'] }}</div>
                    <div class="stat-label">प्रक्रियामा रहेका (छानबिन)</div>
                </div>
                <div class="stat-icon warning"><i data-lucide="history"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-success">{{ $stats['approved_applications'] }}</div>
                    <div class="stat-label">स्वीकृत भएका निवेदनहरू</div>
                </div>
                <div class="stat-icon success"><i data-lucide="check-circle-2"></i></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value text-danger">{{ $stats['rejected_applications'] }}</div>
                    <div class="stat-label">अस्वीकृत / संशोधन माग</div>
                </div>
                <div class="stat-icon danger"><i data-lucide="x-circle"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Applications -->
    <div class="col-12 col-lg-8">
        <div class="card table-card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="history" class="me-2 text-primary"></i>मेरा हालैका निवेदनहरू</h6>
                <a href="{{ route('citizen.applications.index') }}" class="btn btn-sm btn-outline-primary">सबै हेर्नुहोस्</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>निवेदन नं.</th>
                            <th>सेवाको नाम</th>
                            <th>स्थिति</th>
                            <th>पेश गरेको मिति</th>
                            <th>कार्य</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $app)
                            <tr>
                                <td class="fw-bold text-primary">{{ $app->application_number }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $app->service->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $app->service->department->name ?? '' }}</div>
                                </td>
                                <td><span class="badge-status {{ $app->getStatusBadgeClass() }}">{{ $app->getStatusLabel() }}</span></td>
                                <td>{{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('citizen.applications.show', $app) }}" class="btn btn-sm btn-outline-primary">
                                        <i data-lucide="eye"></i> ट्र्याक
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    तपाईंले हालसम्म कुनै पनि सेवाको लागि आवेदन दिनुभएको छैन।<br>
                                    <a href="{{ route('citizen.services.index') }}" class="btn btn-sm btn-primary mt-2">सेवाको लागि आवेदन दिनुहोस्</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Featured Services Grid -->
        <h6 class="fw-bold text-dark mb-3"><i data-lucide="star" class="me-2 text-warning"></i>लोकप्रिय सरकारी सेवाहरू</h6>
        <div class="row g-3">
            @foreach($featuredServices as $srv)
                <div class="col-12 col-md-6">
                    <div class="service-card">
                        <span class="service-dept">{{ $srv->department->name ?? 'नेपाल सरकार' }}</span>
                        <h6>{{ $srv->name }}</h6>
                        <p class="small text-muted mb-2">{{ Str::limit($srv->description, 80) }}</p>
                        <div class="service-meta">
                            <span class="service-fee">{{ $srv->fee > 0 ? 'रु. ' . number_format($srv->fee, 2) : 'निःशुल्क' }}</span>
                            <a href="{{ route('citizen.services.show', $srv) }}" class="btn btn-sm btn-outline-primary">आवेदन दिनुहोस्</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Active Public Notices Sidebar -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i data-lucide="megaphone" class="me-2 text-primary"></i>सार्वजनिक सूचनाहरू</h6>
            </div>
            <div class="card-body p-3">
                @forelse($activeNotices as $notice)
                    <div class="notice-card">
                        <h6 class="fw-bold mb-1 text-dark">{{ $notice->title }}</h6>
                        <span class="text-muted extra-small d-block mb-2"><i data-lucide="calendar" class="me-1"></i>{{ $notice->published_at ? $notice->published_at->format('M d, Y') : '' }}</span>
                        <p class="small text-secondary mb-0">{{ Str::limit($notice->content, 120) }}</p>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted small">यस समयमा कुनै पनि सूचना उपलब्ध छैन।</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
