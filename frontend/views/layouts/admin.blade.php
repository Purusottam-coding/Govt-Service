@extends('layouts.app')

@section('title', 'प्रशासन — ' . ($pageTitle ?? 'ड्यासबोर्ड'))

@section('body')
<!-- Full-Screen Fixed Watermark Backdrop -->
<div class="dashboard-watermark-fixed admin-watermark-offset"></div>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Admin Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Government Logo" style="height: 42px; width: auto;" class="me-2">
        <div>
            <h5 class="mb-0 fw-bold text-white" style="font-size: 1.15rem;">बाह्रदशी गाउँपालिका</h5>
            <small class="text-warning fw-semibold" style="font-size: 0.75rem;">प्रशासकीय नियन्त्रण कक्ष</small>
        </div>
    </div>

    <nav class="sidebar-nav flex-grow-1">
        <div class="nav-section">मुख्य ड्यासबोर्ड</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i> ड्यासबोर्ड
        </a>

        <div class="nav-section">प्रशासन तथा सेवा</div>
        <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
            <i data-lucide="network"></i> मन्त्रालय / विभागहरू
        </a>
        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
            <i data-lucide="settings"></i> सरकारी सेवाहरू
        </a>
        <a href="{{ route('admin.qr-codes.index') }}" class="nav-link {{ request()->routeIs('admin.qr-codes.*') ? 'active' : '' }}">
            <i data-lucide="qr-code"></i> भुक्तानी QR कोड
        </a>
        <a href="{{ route('admin.applications.index') }}" class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
            <i data-lucide="file-text"></i> प्राप्त निवेदनहरू
        </a>

        <div class="nav-section">विश्लेषण तथा प्रतिवेदन</div>
        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i data-lucide="bar-chart-3"></i> शाखा तथा राजस्व रिपोर्ट
        </a>

        <div class="nav-section">नागरिक सेवा</div>
        <a href="{{ route('admin.citizens.index') }}" class="nav-link {{ request()->routeIs('admin.citizens.*') ? 'active' : '' }}">
            <i data-lucide="users"></i> नागरिक सूची
        </a>

        <div class="nav-section">सञ्चार तथा सूचना</div>
        <a href="{{ route('admin.notices.index') }}" class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
            <i data-lucide="megaphone"></i> सूचनाहरू
        </a>
        <a href="{{ route('admin.feedback.index') }}" class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}">
            <i data-lucide="message-square"></i> गुनासो तथा सुझाव
        </a>
    </nav>


</aside>

<!-- Main Content -->
<div class="admin-main">
    <!-- Official Koshi-style Top Bar -->
    <div class="gov-topbar admin-gov-topbar">
        <div class="gov-topbar-inner">
            <div class="gov-topbar-left">
                <i data-lucide="calendar" class="topbar-cal-icon"></i>
                <span class="bilingual-live-date">११ असोज २०८३, आइतबार | Sunday, September 27, 2026</span>
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

    <!-- Top Navbar -->
    <div class="top-navbar d-flex align-items-center justify-content-between px-3 px-lg-4 py-2">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleSidebar()" id="sidebarToggle">
                <i data-lucide="menu"></i>
            </button>
            <div>
                <div class="text-muted extra-small d-none d-sm-block">
                    नेपाल सरकार • कोशी प्रदेश • बाह्रदशी गाउँपालिका
                </div>
                <h1 class="page-title mb-0 fs-5 fw-bold text-dark">{{ $pageTitle ?? 'ड्यासबोर्ड' }}</h1>
            </div>
        </div>
        
        <div class="navbar-user d-flex align-items-center gap-3">
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle d-none d-md-inline-block fw-semibold px-2 py-1">
                <i data-lucide="shield-check" style="width:12px;height:12px;"></i> प्रशासक मोड
            </span>
            <x-notification-bell />
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2 border shadow-sm rounded-pill px-3 py-1" data-bs-toggle="dropdown">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" class="rounded-circle object-fit-cover" style="width:30px;height:30px;">
                    @else
                        <div class="user-avatar" style="width:30px;height:30px;font-size:0.75rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    @endif
                    <span class="d-none d-sm-inline fw-semibold text-dark">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><span class="dropdown-item-text text-muted small fw-semibold">{{ auth()->user()->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('notifications.index') }}"><i data-lucide="bell" class="me-2 text-primary"></i>सूचना केन्द्र (Notifications)</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i data-lucide="user" class="me-2"></i>मेरो प्रोफाइल</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i data-lucide="log-out" class="me-2"></i>लगआउट</button>
                        </form>
                    </li>
                </ul>
            </div>

            <!-- Right side Nepal Animated Flag (Transparent - Reference: bahradashimun.gov.np) -->
            <div class="navbar-nepal-flag d-none d-sm-flex align-items-center">
                <picture>
                    <source srcset="{{ asset('images/nepal-flag.webp') }}?v=3" type="image/webp">
                    <img src="{{ asset('images/nepal-flag.gif') }}?v=3" alt="" class="nepal-flag-navbar-img">
                </picture>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="content-area fade-in-up">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i data-lucide="check-circle" class="me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i data-lucide="alert-triangle" class="me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i data-lucide="alert-triangle" class="me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

@push('scripts')
<script>
    function toggleSidebar() {
        document.getElementById('adminSidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    }
</script>
@endpush
@endsection
