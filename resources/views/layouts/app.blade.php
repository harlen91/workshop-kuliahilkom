<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = $metaTitle ?? (isset($title) && filled($title) ? $title . ' - KuliahIlkom Workshop' : 'KuliahIlkom Workshop - Platform Workshop & Webinar IT Terdepan');
        $pageDescription = $metaDescription ?? 'Tingkatkan skill rekayasa perangkat lunak, AI, cloud computing, dan cybersecurity melalui workshop dan webinar interaktif bersama praktisi top industri.';
        $pageKeywords = $metaKeywords ?? 'workshop IT, webinar coding, kuliahilkom, pelatihan programming, belajar laravel, fullstack developer indonesia, bootcamp online';
        $pageCanonical = $canonicalUrl ?? url()->current();
        $pageImage = $ogImage ?? config('services.seo.default_image');
        $pageType = $ogType ?? 'website';
        $pageRobots = $metaRobots ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="author" content="KuliahIlkom">
    <meta name="robots" content="{{ $pageRobots }}">
    <link rel="canonical" href="{{ $pageCanonical }}">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:site_name" content="KuliahIlkom Workshop">
    <meta property="og:type" content="{{ $pageType }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $pageCanonical }}">
    <meta property="og:image" content="{{ $pageImage }}">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter / X Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    <!-- Search Engine Verification & Analytics -->
    @if(config('services.seo.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('services.seo.google_site_verification') }}">
    @endif

    @if(config('services.seo.google_analytics_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.seo.google_analytics_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.seo.google_analytics_id') }}');
        </script>
    @endif

    <!-- Schema.org JSON-LD Structured Data Stack -->
    @stack('schema')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind & Vite Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Midtrans Snap JS -->
    @php
        $midtransService = app(\App\Services\MidtransService::class);
        $snapJsUrl = $midtransService->getSnapJsUrl();
        $clientKey = $midtransService->getClientKey();
    @endphp
    <script type="text/javascript" src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Instrument Sans', sans-serif; }
    </style>
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-900 selection:bg-indigo-600 selection:text-white">
    <!-- Background Ambient Glow Effects -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-indigo-200/50 blur-[130px] rounded-full"></div>
        <div class="absolute top-[600px] right-[-100px] w-[500px] h-[400px] bg-purple-200/40 blur-[140px] rounded-full"></div>
        <div class="absolute bottom-20 left-[-100px] w-[500px] h-[400px] bg-sky-200/40 blur-[140px] rounded-full"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 w-full border-b border-slate-200/80 bg-white/85 backdrop-blur-md shadow-2xs">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-600/20 transition-transform group-hover:scale-105">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-bold tracking-tight text-slate-900 group-hover:text-indigo-600 transition-colors">
                        Kuliah<span class="text-indigo-600">Ilkom</span>
                    </span>
                    <span class="ml-1.5 rounded-md bg-indigo-50 px-1.5 py-0.5 text-[10px] font-bold text-indigo-700 border border-indigo-200/80">WORKSHOP</span>
                </div>
            </a>

            <!-- Nav Links Desktop -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors {{ request()->routeIs('home') ? 'text-indigo-600 font-semibold' : '' }}">
                    Semua Event
                </a>
                <a href="{{ route('home') }}#kategori" class="hover:text-indigo-600 transition-colors">
                    Kategori
                </a>
                <a href="{{ route('home') }}#keunggulan" class="hover:text-indigo-600 transition-colors">
                    Fasilitas
                </a>
                <a href="{{ route('home') }}#faq" class="hover:text-indigo-600 transition-colors">
                    Bantuan & FAQ
                </a>
            </nav>

            <!-- User Auth Menu -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Tiket Saya -->
                    <a href="{{ route('participant.tickets') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs sm:text-sm font-medium text-slate-700 hover:border-indigo-300 hover:text-indigo-600 hover:bg-slate-50 shadow-2xs transition-all">
                        <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Tiket Saya
                    </a>

                    <!-- Admin Portal Link if role is admin -->
                    @if(auth()->user()->isAdmin())
                        <a href="/admin" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700 hover:bg-purple-100 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            Panel Admin
                        </a>
                    @endif

                    <!-- User Name & Logout -->
                    <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                        <span class="hidden lg:inline text-xs font-semibold text-slate-700">
                            {{ auth()->user()->name }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Keluar" class="rounded-lg p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-indigo-600 px-3 py-2 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-indigo-600/25 transition-all">
                        Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="mt-20 border-t border-slate-200 bg-white text-slate-600">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Info Kolom -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-600 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-base font-bold text-slate-900">KuliahIlkom Workshop Platform</span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md leading-relaxed">
                        Pusat pelatihan daring, webinar praktisi, dan workshop coding bersertifikat. Dirancang untuk mempercepat penguasaan teknologi terdepan dari para pakar software engineering.
                    </p>
                    <div class="flex items-center gap-2 pt-2 text-xs text-slate-500">
                        <span>Pembayaran Aman Didukung oleh</span>
                        <span class="font-semibold text-slate-800">Midtrans Payment Gateway</span>
                    </div>
                </div>

                <!-- Navigasi -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 tracking-wider uppercase mb-3">Tautan Cepat</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Katalog Workshop</a></li>
                        <li><a href="{{ route('participant.tickets') }}" class="hover:text-indigo-600 transition-colors">Cek Tiket Saya</a></li>
                        <li><a href="/admin" class="hover:text-indigo-600 transition-colors">Admin Dashboard</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-indigo-600 transition-colors">Login Akun</a></li>
                    </ul>
                </div>

                <!-- Metode Bayar & Kontak -->
                <div>
                    <h4 class="text-sm font-bold text-slate-900 tracking-wider uppercase mb-3">Kanal Pembayaran</h4>
                    <div class="flex flex-wrap gap-2 text-xs text-slate-700">
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">QRIS</span>
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">BCA VA</span>
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">Mandiri</span>
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">BNI</span>
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">GoPay</span>
                        <span class="rounded bg-slate-50 border border-slate-200 px-2 py-1 font-mono shadow-2xs">ShopeePay</span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">
                        Hubungi Tim Support: <br />
                        <span class="text-slate-800 font-medium">support@kuliahilkom.id</span>
                    </p>
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} KuliahIlkom. Seluruh hak cipta dilindungi.</p>
                <p class="mt-2 sm:mt-0 flex items-center gap-1">
                    Dibangun dengan <span class="text-indigo-600 font-semibold">Laravel 12</span>, <span class="text-indigo-600 font-semibold">Livewire 3</span> & <span class="text-indigo-600 font-semibold">Filament</span>
                </p>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
