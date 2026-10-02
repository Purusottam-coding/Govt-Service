@extends('layouts.app')

@section('title', ($pageTitle ?? 'ड्यासबोर्ड') . ' — नेपाल सरकार')

@section('body')
<!-- Full-Screen Fixed Watermark Backdrop -->
<div class="dashboard-watermark-fixed"></div>

<div class="citizen-portal-wrapper">
    <!-- Official Top National Info Bar (Matching koshi.gov.np red top bar) -->
    <div class="gov-topbar">
        <div class="gov-topbar-inner">
            <div class="gov-topbar-left">
                <i data-lucide="calendar" class="topbar-cal-icon"></i>
                <span id="govNepaliLiveDate" class="bilingual-live-date">११ असोज २०८३, आइतबार | Sunday, September 27, 2026</span>
                <span class="gov-topbar-divider d-none d-xl-inline text-white-50 ms-3 me-2">|</span>
                <p class="gov-emergency-chip mb-0 d-none d-xl-inline-flex" style="cursor: default;">
                    <i data-lucide="phone-call" style="width: 12px; height: 12px;"></i> एम्बुलेन्स: १०२
                </p>
                <p class="gov-emergency-chip mb-0 d-none d-xl-inline-flex ms-1" style="cursor: default;">
                    <i data-lucide="shield-alert" style="width: 12px; height: 12px;"></i> प्रहरी: १००
                </p>
            </div>

            <div class="gov-topbar-right">
                <div class="font-controls" aria-label="अक्षर साइज नियन्त्रण">
                    <button class="font-btn" type="button" onclick="changeFontSize(-1)" aria-label="अक्षर साइज घटाउनुहोस्" title="अक्षर साइज घटाउनुहोस्">-A</button>
                    <button class="font-btn" type="button" onclick="changeFontSize(1)" aria-label="अक्षर साइज बढाउनुहोस्" title="अक्षर साइज बढाउनुहोस्">+A</button>
                </div>

                <div class="koshi-language-switch" aria-label="Language Selector">
                    <button type="button" class="koshi-lang-btn active" data-lang="ne" onclick="setLanguage('ne')">नेपाली</button>
                    <span class="koshi-lang-pipe">|</span>
                    <button type="button" class="koshi-lang-btn" data-lang="en" onclick="setLanguage('en')">English</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Citizen Navbar -->
    <nav class="navbar navbar-expand-lg citizen-navbar sticky-top">
        <div class="citizen-nav-container d-flex align-items-center justify-content-between">
            <a class="navbar-brand d-inline-flex align-items-center" href="{{ route('citizen.dashboard') }}">
                <div class="brand-emblem-wrap">
                    <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Government Emblem" class="brand-emblem-img me-2">
                </div>
                <div class="brand-text-block">
                    <span class="brand-title-main">बाह्रदशी गाउँपालिका</span>
                    <span class="brand-subtitle-sub d-none d-sm-block">गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा</span>
                </div>
            </a>
            <button class="navbar-toggler text-white border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#citizenNav" aria-controls="citizenNav" aria-expanded="false" aria-label="Toggle navigation">
                <i data-lucide="menu" style="color: #ffffff;"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="citizenNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.dashboard') ? 'active' : '' }}" href="{{ route('citizen.dashboard') }}">
                            <i data-lucide="layout-dashboard"></i>ड्यासबोर्ड
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.services.*') ? 'active' : '' }}" href="{{ route('citizen.services.index') }}">
                            <i data-lucide="briefcase"></i>सरकारी सेवाहरू
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.branches.*') ? 'active' : '' }}" href="{{ route('citizen.branches.index') }}">
                            <i data-lucide="git-branch"></i>विषयगत शाखाहरू
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.verify.*') ? 'active' : '' }}" href="{{ route('citizen.verify.index') }}">
                            <i data-lucide="shield-check"></i>निवेदन प्रमाणीकरण
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.applications.*') ? 'active' : '' }}" href="{{ route('citizen.applications.index') }}">
                            <i data-lucide="file-text"></i>मेरा निवेदनहरू
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.approved-documents.*') ? 'active' : '' }}" href="{{ route('citizen.approved-documents.index') }}">
                            <i data-lucide="award"></i>प्रमाणित कागजातहरू
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('citizen.feedback.*') ? 'active' : '' }}" href="{{ route('citizen.feedback.index') }}">
                            <i data-lucide="message-square-heart"></i>गुनासो / सुझाव
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    @auth
                        <x-notification-bell />
                        <div class="dropdown">
                            <button class="btn btn-sm navbar-user-btn dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover shadow-sm" style="width:30px;height:30px; border: 1.5px solid #fff;">
                                @else
                                    <div class="user-avatar" style="width:30px;height:30px;font-size:.75rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                @endif
                                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><span class="dropdown-item-text text-muted small fw-semibold">{{ auth()->user()->email }}</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i data-lucide="bell" class="me-2 text-primary"></i>सूचना केन्द्र (Notifications)</a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i data-lucide="user" class="me-2"></i>मेरो प्रोफाइल</a></li>
                                <li><a class="dropdown-item" href="{{ route('citizen.branches.index') }}"><i data-lucide="building-2" class="me-2 text-primary"></i>विषयगत शाखाहरू निर्देशिका</a></li>
                                <li><a class="dropdown-item" href="{{ route('citizen.verify.index') }}"><i data-lucide="shield-check" class="me-2 text-success"></i>शाखा तथा निवेदन प्रमाणीकरण</a></li>
                                <li><a class="dropdown-item" href="{{ route('citizen.applications.index') }}"><i data-lucide="file-check-2" class="me-2"></i>मेरो निवेदन स्थिति</a></li>
                                <li><a class="dropdown-item" href="{{ route('citizen.approved-documents.index') }}"><i data-lucide="award" class="me-2 text-success"></i>प्रमाणित कागजातहरू</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"><i data-lucide="log-out" class="me-2"></i>लगआउट</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light fw-bold px-3">
                            <i data-lucide="log-in" class="me-1"></i> लगइन / दर्ता
                        </a>
                    @endauth

                    <!-- Right side Nepal Animated Flag (Transparent - Reference: bahradashimun.gov.np) -->
                    <div class="navbar-nepal-flag d-none d-sm-flex align-items-center">
                        <picture>
                            <source srcset="{{ asset('images/nepal-flag.webp') }}?v=3" type="image/webp">
                            <img src="{{ asset('images/nepal-flag.gif') }}?v=3" alt="" class="nepal-flag-navbar-img">
                        </picture>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="citizen-main fade-in-up">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i data-lucide="check-circle" class="me-2 text-success"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i data-lucide="alert-triangle" class="me-2 text-danger"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i data-lucide="info" class="me-2 text-info"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i data-lucide="alert-triangle" class="me-2 text-danger"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Official Government Footer -->
    <footer class="gov-footer">
        <div class="gov-footer-main">
            <div class="row g-4">
                <!-- Col 1: About Municipality -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="gov-footer-brand">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Emblem" style="height: 48px; width: auto;">
                            <div>
                                <h4 class="mb-0">बाह्रदशी गाउँपालिका</h4>
                                <span class="small text-warning">गाउँ कार्यपालिकाको कार्यालय, झापा</span>
                            </div>
                        </div>
                        <p class="mb-3">
                            नेपाल सरकारको "डिजिटल नेपाल फ्रेमवर्क" अन्तर्गत बाह्रदशी गाउँपालिकाका सम्पूर्ण नागरिकहरूलाई छिटो, छरितो र पारदर्शी सरकारी सेवा प्रवाह गर्न यो अनलाइन पोर्टल सञ्चालन गरिएको हो।
                        </p>
                        <div class="text-white-50 small fst-italic">
                            "समृद्ध बाह्रदशीको आधार: कृषि, शिक्षा, स्वास्थ्य र पूर्वाधार"
                        </div>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="col-6 col-md-3 col-lg-2">
                    <h6 class="gov-footer-heading">महत्वपूर्ण लिंकहरू</h6>
                    <ul class="gov-footer-links">
                        <li><a href="{{ route('citizen.dashboard') }}"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i>ड्यासबोर्ड</a></li>
                        <li><a href="{{ route('citizen.services.index') }}"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i>सरकारी सेवाहरू</a></li>
                        <li><a href="{{ route('citizen.applications.index') }}"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i>मेरा निवेदनहरू</a></li>
                        <li><a href="{{ route('citizen.feedback.index') }}"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i>गुनासो तथा सुझाव</a></li>
                        <li><a href="{{ route('profile.edit') }}"><i data-lucide="chevron-right" style="width:14px;height:14px;"></i>नागरिक प्रोफाइल</a></li>
                    </ul>
                </div>

                <!-- Col 3: External Government Portals -->
                <div class="col-6 col-md-3 col-lg-3">
                    <h6 class="gov-footer-heading">सरकारी निकायहरू</h6>
                    <ul class="gov-footer-links">
                        <li><a href="https://nepal.gov.np" target="_blank"><i data-lucide="external-link" style="width:14px;height:14px;"></i>नेपाल सरकार पोर्टल</a></li>
                        <li><a href="https://koshi.gov.np" target="_blank"><i data-lucide="external-link" style="width:14px;height:14px;"></i>कोशी प्रदेश सरकार</a></li>
                        <li><a href="https://mofaga.gov.np" target="_blank"><i data-lucide="external-link" style="width:14px;height:14px;"></i>सङ्घीय मामिला मन्त्रालय</a></li>
                        <li><a href="https://www.bahradashimun.gov.np" target="_blank"><i data-lucide="external-link" style="width:14px;height:14px;"></i>बाह्रदशी आधिकारिक वेबसाइट</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Hours -->
                <div class="col-12 col-md-6 col-lg-3">
                    <h6 class="gov-footer-heading">सम्पर्क तथा कार्यालय</h6>
                    <div class="gov-footer-contact-item">
                        <i data-lucide="map-pin"></i>
                        <span>चकचकी, झापा, कोशी प्रदेश, नेपाल</span>
                    </div>
                    <div class="gov-footer-contact-item">
                        <i data-lucide="phone"></i>
                        <span>०२३-५८०१११, ९८५२६xxxxx</span>
                    </div>
                    <div class="gov-footer-contact-item">
                        <i data-lucide="mail"></i>
                        <span>info@bahradashimun.gov.np</span>
                    </div>
                    <div class="gov-footer-contact-item">
                        <i data-lucide="clock"></i>
                        <span>आइत - बिही: १०:०० - ५:०० | शुक्र: १०:०० - ३:००</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="gov-footer-bottom">
            <div class="gov-footer-bottom-inner">
                <div>
                    © २०८१ बाह्रदशी गाउँपालिका, गाउँ कार्यपालिकाको कार्यालय • सर्वाधिकार सुरक्षित
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span>Digital Nepal e-Governance Standard</span>
                    <span>•</span>
                    <span>सुरक्षित सरकारी सेवा</span>
                </div>
            </div>
        </div>
    </footer>
</div>

@if(session('payment_verification_popup'))
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success-subtle">
                    <h5 class="modal-title fw-bold" id="verificationModalLabel">
                        <i data-lucide="badge-check" class="me-1"></i> भुक्तानी सूचना
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ session('payment_verification_popup_message', 'तपाईंको भुक्तानी प्रमाण verification process मा पठाइएको छ।') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">ठीक छ</button>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
@if(session('payment_verification_popup'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('verificationModal');
        if (!modalElement) {
            return;
        }

        const verificationModal = new bootstrap.Modal(modalElement);
        verificationModal.show();
    });
</script>
@endif
@endpush
