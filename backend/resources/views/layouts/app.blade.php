<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'सरकारी सेवा प्रणाली') — नेपाल सरकार</title>
    <meta name="description" content="@yield('meta_description', 'नेपाल सरकार अनलाइन सरकारी सेवा प्रणाली — सेवाहरूका लागि अनलाइन आवेदन दिनुहोस् र स्थिति ट्र्याक गर्नुहोस्।')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@300;400;500;600;700;800&family=Noto+Sans+Devanagari:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS with cache-busting -->
    <link href="{{ asset('css/custom.css') }}?v={{ time() }}" rel="stylesheet">

    @stack('styles')

    <style>
        /* Permanent override: prevent Google Translate from highlighting/selecting text on hover */
        font,
        font[style*="vertical-align"],
        font.goog-text-highlight,
        .goog-text-highlight,
        span.goog-text-highlight {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            outline: none !important;
            text-decoration: inherit !important;
            cursor: inherit !important;
            pointer-events: none !important;
        }

        html body font,
        html body font:hover,
        html body font.goog-text-highlight,
        html body .goog-text-highlight,
        html body .goog-text-highlight:hover,
        html body *[class*="goog-text-highlight"] {
            background: transparent !important;
            background-color: transparent !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            outline: none !important;
            text-decoration: inherit !important;
            color: inherit !important;
            pointer-events: none !important;
        }

        /* Ensure all clickable controls stay clickable */
        a, button, select, input, textarea, label, [role="button"], .koshi-lang-btn, .font-btn {
            pointer-events: auto !important;
        }
        a font, button font, label font, [role="button"] font {
            pointer-events: none !important;
        }

        /* Hide Google Translate popup tooltips, original text previews, and balloon frames */
        #goog-gt-tt, 
        #goog-gt-vt,
        .goog-te-balloon-frame,
        .goog-tooltip,
        .goog-tooltip:hover,
        .VIpgJd-ZVi9od-aZ2wEe-wOHMyf,
        .VIpgJd-ZVi9od-aZ2wEe-OkiMDh,
        .VIpgJd-yAWNEb-hvhgl-bN97Pc,
        .VIpgJd-ZVi9od-aZ2wEe,
        .VIpgJd-ZVi9od-v2WNKv {
            display: none !important;
            visibility: hidden !important;
            pointer-events: none !important;
            opacity: 0 !important;
            position: fixed !important;
            top: -99999px !important;
            left: -99999px !important;
            z-index: -99999 !important;
            width: 0 !important;
            height: 0 !important;
        }
    </style>
</head>
<body>
    @yield('body')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')

    <!-- Hidden Google Translate Element -->
    <div id="google_translate_element" style="display:none; position:absolute; left:-9999px;"></div>

    <script>
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'ne',
                includedLanguages: 'ne,en',
                autoDisplay: false
            }, 'google_translate_element');
        }

        function setLanguage(lang) {
            localStorage.setItem('portal_lang', lang);

            // Set cookie for Google Translate
            if (lang === 'en') {
                document.cookie = "googtrans=/ne/en; path=/";
                document.cookie = "googtrans=/ne/en; domain=" + window.location.hostname + "; path=/";
            } else {
                document.cookie = "googtrans=/ne/ne; path=/";
                document.cookie = "googtrans=/ne/ne; domain=" + window.location.hostname + "; path=/";
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; domain=" + window.location.hostname + "; path=/;";
            }

            // Trigger Google Translate select element if loaded
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = lang;
                select.dispatchEvent(new Event('change'));
            }

            updateLangUi(lang);

            setTimeout(function() {
                window.location.reload();
            }, 120);
        }

        function updateLangUi(lang) {
            document.querySelectorAll('.koshi-lang-btn').forEach(btn => {
                if (btn.getAttribute('data-lang') === lang) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        function initBilingualNepalDate() {
            const dateObj = new Date();
            const daysEn = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const monthsEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            
            const dayNameEn = daysEn[dateObj.getDay()];
            const monthNameEn = monthsEn[dateObj.getMonth()];
            const dateNumEn = dateObj.getDate();
            const yearEn = dateObj.getFullYear();
            const englishStr = `${dayNameEn}, ${monthNameEn} ${dateNumEn}, ${yearEn}`;

            // Benchmark benchmark: 2026-09-27 corresponds to 2083-06-11 (११ असोज २०८३, आइतबार)
            const benchmarkAd = new Date(2026, 8, 27);
            const benchmarkBsYear = 2083;
            const benchmarkBsMonth = 5; // 0-indexed: 5 = असोज
            const benchmarkBsDay = 11;
            
            const diffTime = dateObj.getTime() - benchmarkAd.getTime();
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
            
            const bsMonthDays = [31, 31, 32, 32, 31, 30, 30, 30, 29, 30, 29, 31];
            const nepaliMonths = ['बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज', 'कात्तिक', 'मंसिर', 'पुस', 'माघ', 'फागुन', 'चैत'];
            const nepaliDays = ['आइतबार', 'सोमबार', 'मंगलबार', 'बुधबार', 'बिहीबार', 'शुक्रबार', 'शनिबार'];
            const nepaliDigits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

            let bsDay = benchmarkBsDay + diffDays;
            let bsMonth = benchmarkBsMonth;
            let bsYear = benchmarkBsYear;

            while (bsDay > bsMonthDays[bsMonth]) {
                bsDay -= bsMonthDays[bsMonth];
                bsMonth++;
                if (bsMonth > 11) {
                    bsMonth = 0;
                    bsYear++;
                }
            }
            while (bsDay < 1) {
                bsMonth--;
                if (bsMonth < 0) {
                    bsMonth = 11;
                    bsYear--;
                }
                bsDay += bsMonthDays[bsMonth];
            }

            const toNepaliNum = (num) => String(num).split('').map(d => nepaliDigits[d] || d).join('');
            const nepaliDayName = nepaliDays[dateObj.getDay()];
            const nepaliMonthName = nepaliMonths[bsMonth];
            const nepaliStr = `${toNepaliNum(bsDay)} ${nepaliMonthName} ${toNepaliNum(bsYear)}, ${nepaliDayName}`;

            const fullBilingualDate = `${nepaliStr} | ${englishStr}`;

            document.querySelectorAll('.bilingual-live-date').forEach(el => {
                el.textContent = fullBilingualDate;
            });
        }

        function removeGoogleTranslateBanner() {
            if (document.body) {
                document.body.style.setProperty('top', '0px', 'important');
                document.body.style.setProperty('position', 'static', 'important');
            }
            if (document.documentElement) {
                document.documentElement.style.setProperty('top', '0px', 'important');
            }
            const elements = document.querySelectorAll('iframe.skiptranslate, .skiptranslate iframe, iframe.goog-te-banner-frame, iframe[id*="container"], .VIpgJd-ZVi9od-ORLlD-bN97Pc-haAclf');
            elements.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
                el.style.setProperty('visibility', 'hidden', 'important');
                el.style.setProperty('height', '0px', 'important');
                el.style.setProperty('width', '0px', 'important');
                el.style.setProperty('position', 'absolute', 'important');
                el.style.setProperty('top', '-9999px', 'important');
            });
        }

        // Font resize control (-A / +A) like koshi.gov.np
        function changeFontSize(delta) {
            const root = document.documentElement;
            let current = parseFloat(window.getComputedStyle(root).fontSize) || 16;
            let newSize = current + delta;
            if (newSize >= 12 && newSize <= 22) {
                root.style.fontSize = newSize + 'px';
                localStorage.setItem('portal_font_size', newSize);
            }
        }

        function removeGoogleTranslateBanner() {
            if (document.body) {
                document.body.style.setProperty('top', '0px', 'important');
                document.body.style.setProperty('position', 'static', 'important');
            }
            if (document.documentElement) {
                document.documentElement.style.setProperty('top', '0px', 'important');
            }
            const elements = document.querySelectorAll('iframe.skiptranslate, .skiptranslate iframe, iframe.goog-te-banner-frame, iframe[id*="container"], .VIpgJd-ZVi9od-ORLlD-bN97Pc-haAclf');
            elements.forEach(function(el) {
                el.style.setProperty('display', 'none', 'important');
                el.style.setProperty('visibility', 'hidden', 'important');
                el.style.setProperty('height', '0px', 'important');
                el.style.setProperty('width', '0px', 'important');
                el.style.setProperty('position', 'absolute', 'important');
                el.style.setProperty('top', '-9999px', 'important');
            });

            // Neutralize and strip font hover selection
            document.querySelectorAll('font').forEach(function(f) {
                if (f.style.pointerEvents !== 'none') {
                    f.style.setProperty('pointer-events', 'none', 'important');
                }
                if (f.classList.contains('goog-text-highlight')) {
                    f.classList.remove('goog-text-highlight');
                }
                f.style.setProperty('background', 'transparent', 'important');
                f.style.setProperty('box-shadow', 'none', 'important');
            });

            // Remove tooltip container if injected
            const tt = document.getElementById('goog-gt-tt');
            if (tt) {
                tt.style.setProperty('display', 'none', 'important');
                tt.style.setProperty('visibility', 'hidden', 'important');
            }
        }

        setInterval(removeGoogleTranslateBanner, 50);

        document.addEventListener('DOMContentLoaded', function() {
            const currentLang = localStorage.getItem('portal_lang') || (document.cookie.includes('googtrans=/ne/en') ? 'en' : 'ne');
            updateLangUi(currentLang);
            initBilingualNepalDate();
            removeGoogleTranslateBanner();

            const savedFontSize = localStorage.getItem('portal_font_size');
            if (savedFontSize) {
                document.documentElement.style.fontSize = savedFontSize + 'px';
            }

            const observer = new MutationObserver(function() {
                removeGoogleTranslateBanner();
            });
            observer.observe(document.documentElement, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        });

        // Intercept and neutralize mouse events on font tags so Google hover timer never fires
        ['mouseover', 'mouseenter', 'pointerover', 'mousemove', 'pointermove'].forEach(function(eventType) {
            window.addEventListener(eventType, function(e) {
                if (e.target && (e.target.tagName === 'FONT' || (e.target.closest && e.target.closest('font')) || (e.target.classList && e.target.classList.contains('goog-text-highlight')))) {
                    if (e.target.classList && e.target.classList.contains('goog-text-highlight')) {
                        e.target.classList.remove('goog-text-highlight');
                    }
                    e.target.style.setProperty('background', 'transparent', 'important');
                    e.target.style.setProperty('background-color', 'transparent', 'important');
                    e.target.style.setProperty('box-shadow', 'none', 'important');
                    e.target.style.setProperty('-webkit-box-shadow', 'none', 'important');
                    e.target.style.setProperty('pointer-events', 'none', 'important');
                    e.stopImmediatePropagation();
                }
            }, true);
        });
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    @stack('modals')

    <script>
        // Ensure all Bootstrap modals escape nested stacking contexts so backdrops never overlay them
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal').forEach(function (modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        });
    </script>
</body>
</html>
