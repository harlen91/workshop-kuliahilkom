<div class="space-y-16 pb-16">
    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-16 md:pt-20 md:pb-24 overflow-hidden">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <!-- Badge Promo -->
            <div class="inline-flex items-center gap-2 rounded-full border border-indigo-500/30 bg-indigo-500/10 px-4 py-1.5 text-xs sm:text-sm font-medium text-indigo-300 backdrop-blur-md mb-6 shadow-sm shadow-indigo-500/20">
                <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Pendaftaran Gelombang Terbaru Dibuka • Sertifikat Resmi</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white max-w-4xl mx-auto leading-tight">
                Kuasai Skill IT Terkini Bersama <br class="hidden sm:inline" />
                <span class="bg-gradient-to-r from-indigo-400 via-purple-300 to-pink-400 bg-clip-text text-transparent">
                    Praktisi & Tech Lead Terbaik
                </span>
            </h1>

            <p class="mt-6 text-base sm:text-lg md:text-xl text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Pelajari Laravel, Artificial Intelligence, Cloud DevOps, hingga Cyber Security secara praktis. Dapatkan rekaman materi seumur hidup dan sertifikat ber-QR Code.
            </p>

            <!-- Search Bar Floating Input -->
            <div class="mt-8 sm:mt-10 max-w-2xl mx-auto">
                <div class="relative flex items-center rounded-2xl border border-slate-700/80 bg-slate-900/90 p-2 shadow-2xl shadow-indigo-950/50 backdrop-blur-xl focus-within:border-indigo-500 transition-all">
                    <div class="pl-3 pr-2 text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari topik workshop, webinar, instruktur, atau skill..." 
                        class="w-full bg-transparent px-2 py-2 text-sm sm:text-base text-white placeholder-slate-400 focus:outline-none"
                    />
                    @if($search)
                        <button wire:click="$set('search', '')" class="p-1 text-slate-400 hover:text-white mr-1">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto border-y border-slate-800/80 py-6 text-center">
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white">{{ number_format($stats['total_alumni'], 0, ',', '.') }}+</div>
                    <div class="text-xs sm:text-sm text-slate-400 mt-1">Alumni Terdaftar</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-indigo-400">{{ $stats['total_events'] }}+</div>
                    <div class="text-xs sm:text-sm text-slate-400 mt-1">Workshop & Webinar</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-400">{{ $stats['satisfaction_rate'] }}</div>
                    <div class="text-xs sm:text-sm text-slate-400 mt-1">Tingkat Kepuasan</div>
                </div>
                <div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-purple-400">100%</div>
                    <div class="text-xs sm:text-sm text-slate-400 mt-1">Praktisi Industri</div>
                </div>
            </div>
        </div>
    </section>

    <!-- KATALOG WORKSHOP SECTION -->
    <section id="katalog" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 scroll-mt-20">
        <!-- Filter Bar -->
        <div class="space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Daftar Workshop & Webinar</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">Pilih materi yang sesuai dengan minat karir Anda</p>
                </div>

                <!-- Secondary Filter Dropdowns -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Filter Tipe -->
                    <select wire:model.live="selectedType" class="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs sm:text-sm text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Tipe Event</option>
                        <option value="webinar">Webinar Online</option>
                        <option value="workshop">Workshop Hands-on</option>
                        <option value="bootcamp">Bootcamp Intensif</option>
                    </select>

                    <!-- Filter Harga -->
                    <select wire:model.live="selectedPrice" class="rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-xs sm:text-sm text-slate-200 focus:border-indigo-500 focus:outline-none">
                        <option value="">Semua Harga</option>
                        <option value="free">Gratis (Free)</option>
                        <option value="paid">Berbayar</option>
                    </select>

                    @if($selectedCategory || $selectedType || $selectedPrice || $search)
                        <button wire:click="resetFilters" class="rounded-xl border border-rose-500/30 bg-rose-500/10 px-3 py-2 text-xs sm:text-sm font-medium text-rose-300 hover:bg-rose-500/20 transition-colors">
                            Reset Filter
                        </button>
                    @endif
                </div>
            </div>

            <!-- Category Pills Filter -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <button 
                    wire:click="setCategory(null)" 
                    class="rounded-full px-4 py-1.5 text-xs sm:text-sm font-medium transition-all whitespace-nowrap {{ is_null($selectedCategory) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:border-slate-700' }}"
                >
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button 
                        wire:click="setCategory('{{ $cat->slug }}')" 
                        class="rounded-full px-4 py-1.5 text-xs sm:text-sm font-medium transition-all whitespace-nowrap {{ $selectedCategory === $cat->slug ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:border-slate-700' }}"
                    >
                        {{ $cat->name }}
                        @if($cat->workshops_count > 0)
                            <span class="ml-1 rounded-full bg-slate-800 px-1.5 py-0.2 text-[10px] text-slate-300">{{ $cat->workshops_count }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Workshops Grid -->
        <div class="mt-8">
            @if($workshops->isEmpty())
                <div class="rounded-2xl border border-slate-800/80 bg-slate-900/40 p-12 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-400 mb-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Tidak ada workshop ditemukan</h3>
                    <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">Coba ubah kata kunci pencarian atau sesuaikan opsi filter kategori di atas.</p>
                    <button wire:click="resetFilters" class="mt-4 rounded-xl bg-indigo-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white hover:bg-indigo-500 transition-colors">
                        Tampilkan Semua Workshop
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($workshops as $workshop)
                        <article class="group flex flex-col justify-between rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5 backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-950/40">
                            <!-- Banner Image & Badges -->
                            <div>
                                <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-800">
                                    <img 
                                        src="{{ $workshop->banner_url }}" 
                                        alt="{{ $workshop->title }}" 
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        loading="lazy"
                                    />
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                    
                                    <!-- Badges Top -->
                                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                                        <span class="rounded-lg px-2.5 py-1 text-[11px] font-bold tracking-wider uppercase backdrop-blur-md shadow-sm {{ $workshop->type === 'workshop' ? 'bg-indigo-500/90 text-white' : ($workshop->type === 'bootcamp' ? 'bg-amber-500/90 text-slate-950' : 'bg-sky-500/90 text-white') }}">
                                            {{ $workshop->type }}
                                        </span>
                                        <span class="rounded-lg bg-slate-900/80 px-2.5 py-1 text-[11px] font-medium text-slate-300 border border-slate-700/60 backdrop-blur-md">
                                            {{ $workshop->category?->name ?? 'General' }}
                                        </span>
                                    </div>

                                    <!-- Price Badge Bottom Right -->
                                    <div class="absolute bottom-3 right-3">
                                        @if($workshop->isFree())
                                            <span class="rounded-lg bg-emerald-500 px-3 py-1 text-xs font-extrabold text-slate-950 shadow-lg">
                                                GRATIS
                                            </span>
                                        @else
                                            <div class="flex items-center gap-1.5 rounded-lg bg-slate-900/90 px-3 py-1 backdrop-blur-md border border-slate-700/80">
                                                @if($workshop->formattedOriginalPrice())
                                                    <span class="text-[11px] text-slate-400 line-through">{{ $workshop->formattedOriginalPrice() }}</span>
                                                @endif
                                                <span class="text-xs font-bold text-indigo-400">{{ $workshop->formattedPrice() }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Schedule & Platform -->
                                <div class="mt-4 flex items-center gap-4 text-xs text-slate-400">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $workshop->start_date->isoFormat('D MMM Y, HH:mm') }} {{ $workshop->timezone }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span class="capitalize">{{ $workshop->platform }}</span>
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3 class="mt-2.5 text-lg font-bold text-white group-hover:text-indigo-400 transition-colors line-clamp-2">
                                    <a href="{{ route('workshop.detail', $workshop->slug) }}">
                                        {{ $workshop->title }}
                                    </a>
                                </h3>

                                <p class="mt-2 text-xs sm:text-sm text-slate-400 line-clamp-2 leading-relaxed">
                                    {{ $workshop->short_description }}
                                </p>
                            </div>

                            <!-- Bottom: Speaker & CTA -->
                            <div class="mt-5 pt-4 border-t border-slate-800/80">
                                <!-- Speaker Card Mini -->
                                @if($workshop->speaker)
                                    <div class="flex items-center gap-3 mb-4">
                                        <img 
                                            src="{{ $workshop->speaker->photo_url }}" 
                                            alt="{{ $workshop->speaker->name }}" 
                                            class="h-9 w-9 rounded-full object-cover border border-slate-700"
                                        />
                                        <div class="min-w-0">
                                            <div class="text-xs font-semibold text-slate-200 truncate">{{ $workshop->speaker->name }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ $workshop->speaker->title_role }}</div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Quota Info -->
                                @if($workshop->quota)
                                    <div class="mb-3">
                                        <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                                            <span>Sisa Kuota: <strong class="text-white">{{ $workshop->remaining_quota }} kursi</strong></span>
                                            <span>Total {{ $workshop->quota }}</span>
                                        </div>
                                        @php
                                            $percentage = $workshop->quota > 0 ? round((($workshop->quota - $workshop->remaining_quota) / $workshop->quota) * 100) : 0;
                                        @endphp
                                        <div class="h-1.5 w-full rounded-full bg-slate-800 overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </div>
                                @endif

                                <!-- CTA Button -->
                                <a 
                                    href="{{ route('workshop.detail', $workshop->slug) }}" 
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-800/90 px-4 py-2.5 text-xs sm:text-sm font-semibold text-white border border-slate-700/80 hover:bg-indigo-600 hover:border-indigo-500 hover:shadow-lg hover:shadow-indigo-600/25 transition-all"
                                >
                                    <span>Lihat Rincian & Daftar</span>
                                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- FASILITAS / KEUNGGULAN SECTION -->
    <section id="keunggulan" class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-10 scroll-mt-20">
        <div class="rounded-3xl border border-slate-800 bg-gradient-to-b from-slate-900/80 to-slate-950 p-8 sm:p-12">
            <div class="text-center max-w-2xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Kenapa Belajar di KuliahIlkom Workshop?</h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">Didesain khusus untuk mempersiapkan skill teknis Anda sesuai kebutuhan industri teknologi terkini.</p>
            </div>

            <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/20 text-indigo-400 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Hands-on Practice</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">Bukan sekadar teori. Anda langsung coding dan membangun aplikasi nyata step-by-step.</p>
                </div>

                <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-500/20 text-purple-400 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white">E-Sertifikat Resmi</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">Dilengkapi kode verifikasi unik yang dapat dicantumkan pada CV dan profil LinkedIn Anda.</p>
                </div>

                <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-500/20 text-pink-400 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Rekaman Seumur Hidup</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">Akses rekaman kelas Full HD dan slide materi kapan saja tanpa batasan kedaluwarsa.</p>
                </div>

                <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400 mb-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-white">Grup Diskusi & Q&A</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">Berjejaring dengan sesama developer dan konsultasi langsung dengan instruktur.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section id="faq" class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 pt-10 scroll-mt-20">
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-2">Informasi penting seputar pendaftaran, akses room, dan pembayaran</p>
        </div>

        <div class="mt-8 space-y-4" x-data="{ open: null }">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <button @click="open = open === 1 ? null : 1" class="flex w-full items-center justify-between text-left text-sm sm:text-base font-semibold text-white">
                    <span>Bagaimana cara mengikuti webinar / workshop setelah mendaftar?</span>
                    <span :class="open === 1 ? 'rotate-180' : ''" class="transition-transform duration-200 text-indigo-400">▼</span>
                </button>
                <div x-show="open === 1" x-collapse class="mt-3 text-xs sm:text-sm text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                    Setelah Anda mendaftar (atau menyelesaikan pembayaran untuk event berbayar), tiket resmi Anda akan langsung aktif. Anda dapat melihat link Zoom / Google Meet beserta passcode pada menu <strong>"Tiket Saya"</strong> di dashboard Anda.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <button @click="open = open === 2 ? null : 2" class="flex w-full items-center justify-between text-left text-sm sm:text-base font-semibold text-white">
                    <span>Metode pembayaran apa saja yang didukung oleh Midtrans?</span>
                    <span :class="open === 2 ? 'rotate-180' : ''" class="transition-transform duration-200 text-indigo-400">▼</span>
                </button>
                <div x-show="open === 2" x-collapse class="mt-3 text-xs sm:text-sm text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                    Kami mendukung seluruh kanal pembayaran resmi di Indonesia melalui Midtrans Snap Pop-up: QRIS (GoPay, OVO, ShopeePay, DANA), Virtual Account Bank (BCA, Mandiri, BNI, BRI, Permata), Kartu Kredit/Debit, dan gerai Indomaret/Alfamart. Konfirmasi pembayaran berjalan 100% otomatis seketika.
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5">
                <button @click="open = open === 3 ? null : 3" class="flex w-full items-center justify-between text-left text-sm sm:text-base font-semibold text-white">
                    <span>Apakah saya tetap mendapatkan rekaman jika berhalangan hadir saat live?</span>
                    <span :class="open === 3 ? 'rotate-180' : ''" class="transition-transform duration-200 text-indigo-400">▼</span>
                </button>
                <div x-show="open === 3" x-collapse class="mt-3 text-xs sm:text-sm text-slate-400 leading-relaxed border-t border-slate-800/60 pt-3">
                    Ya, seluruh peserta yang telah terdaftar berhak mengakses rekaman video Full HD dan seluruh slide atau source code proyek workshop yang akan diunggah pada dashboard peserta pasca acara selesai.
                </div>
            </div>
        </div>
    </section>
</div>
