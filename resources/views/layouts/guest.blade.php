@extends('layouts.app')

@section('body')
<div class="auth-split-wrapper">
    <!-- Top-Right Language Switcher on Login Page (Like koshi.gov.np) -->
    <div class="auth-top-lang-switch">
        <div class="koshi-language-switch light-theme" aria-label="Language Selector">
            <button type="button" class="koshi-lang-btn active" data-lang="ne" onclick="setLanguage('ne')">नेपाली</button>
            <span class="koshi-lang-pipe">|</span>
            <button type="button" class="koshi-lang-btn" data-lang="en" onclick="setLanguage('en')">English</button>
        </div>
    </div>

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
            <div class="auth-footer-links mt-3">
                <p class="text-muted small mb-0 text-center" style="font-size: 0.82rem; line-height: 1.5;">
                    <i data-lucide="shield-check" class="text-success" style="width: 15px; height: 15px; display: inline-block; vertical-align: -2px; margin-right: 4px;"></i><span>बाह्रदशी गाउँपालिका, झापा • सुरक्षित सरकारी विद्युतीय सेवा प्रणाली</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
