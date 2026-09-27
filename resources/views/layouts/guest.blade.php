@extends('layouts.app')

@section('body')
<div class="auth-split-wrapper">
    <!-- Left Hero Showcase Pane -->
    <div class="auth-hero-pane">
        <div class="auth-hero-overlay"></div>
        <div class="auth-hero-content">
            <div class="auth-hero-emblem">
                <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Government Logo">
            </div>
            <h1 class="auth-hero-title-np">बाह्रदशी गाउँपालिका</h1>
            <h2 class="auth-hero-title-en">Barhadashi Rural Municipality</h2>
            <p class="auth-hero-sub-np">गाउँ कार्यपालिकाको कार्यालय, चकचकी, झापा</p>
            <p class="auth-hero-sub-en">Office of the Rural Municipal Executive • Koshi Province, Nepal</p>
            
            <div class="auth-hero-meta mb-4">
                <span>डिजिटल नेपाल e-Governance पोर्टल</span>
                <span class="auth-hero-separator">•</span>
                <span>जननी जन्मभूमिश्च स्वर्गादपि गरीयसी</span>
            </div>

            <div class="auth-feature-list">
                <div class="auth-feature-item">
                    <i data-lucide="award"></i>
                    <span>अनलाइन सिफारिस तथा प्रमाणित विद्युतीय प्रमाणपत्र</span>
                </div>
                <div class="auth-feature-item">
                    <i data-lucide="qr-code"></i>
                    <span>द्रुत तथा सुरक्षित डिजिटल राजस्व भुक्तानी (QR Pay)</span>
                </div>
                <div class="auth-feature-item">
                    <i data-lucide="shield-check"></i>
                    <span>नागरिक अधिकार, गोपनीयता तथा प्रत्यक्ष गुनासो सुनुवाइ</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Form Pane -->
    <div class="auth-form-pane">
        <div class="auth-form-container">
            <div class="auth-card-box">
                <!-- Crimson Header Box -->
                <div class="auth-card-header">
                    <div class="auth-header-emblem">
                        <img src="{{ asset('images/Emblem_of_Nepal.png') }}" alt="Nepal Government Emblem">
                    </div>
                    <h4 class="auth-header-title-np">बाह्रदशी गाउँपालिका</h4>
                    <p class="auth-header-title-en">Barhadashi Rural Municipality</p>
                    <span class="auth-header-badge">
                        <i data-lucide="lock" style="width: 12px; height: 12px;" class="me-1"></i>
                        नागरिक तथा कर्मचारी आधिकारिक लगइन पोर्टल
                    </span>
                </div>

                <!-- Card Content -->
                <div class="auth-card-body">
                    @yield('content')
                </div>
            </div>

            <!-- Footer Info -->
            <div class="auth-footer-links text-center mt-3">
                <span class="text-muted small d-inline-flex align-items-center gap-1">
                    <i data-lucide="shield-check" class="text-success" style="width: 14px; height: 14px;"></i>
                    बाह्रदशी गाउँपालिका, झापा • सुरक्षित सरकारी विद्युतीय सेवा प्रणाली
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
