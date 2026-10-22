<div class="py-10" x-data="workshopDetailHandler()">
    <!-- BREADCRUMB -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mb-6">
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <a href="{{ route('home') }}" class="hover:text-indigo-600 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('home') }}?category={{ $workshop->category?->slug }}" class="hover:text-indigo-600 transition-colors">
                {{ $workshop->category?->name ?? 'Workshop' }}
            </a>
            <span>/</span>
            <span class="text-slate-900 truncate max-w-[200px] sm:max-w-md font-semibold">{{ $workshop->title }}</span>
        </nav>
    </div>

    <!-- MAIN EVENT HERO -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- LEFT 2 COLUMNS: Detail, Rundown, Speaker -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Header Card -->
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs">
                    <!-- Badges Row -->
                    <div class="flex flex-wrap items-center gap-2.5 mb-4">
                        <span class="rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wider {{ $workshop->type === 'workshop' ? 'bg-indigo-600 text-white' : ($workshop->type === 'bootcamp' ? 'bg-amber-500 text-white' : 'bg-sky-600 text-white') }}">
                            {{ $workshop->type }}
                        </span>
                        <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                            {{ $workshop->category?->name ?? 'Teknologi' }}
                        </span>
                        @if($workshop->isFree())
                            <span class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-extrabold text-white shadow-sm">
                                GRATIS
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                        {{ $workshop->title }}
                    </h1>

                    <p class="mt-4 text-sm sm:text-base text-slate-600 leading-relaxed">
                        {{ $workshop->short_description }}
                    </p>

                    <!-- Event Metadata Pill Bar -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 border-t border-slate-100 pt-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Jadwal Acara</div>
                                <div class="text-xs sm:text-sm font-bold text-slate-900">{{ $workshop->start_date->isoFormat('D MMMM Y') }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Waktu & Durasi</div>
                                <div class="text-xs sm:text-sm font-bold text-slate-900">{{ $workshop->start_date->format('H:i') }} - {{ $workshop->end_date->format('H:i') }} {{ $workshop->timezone }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Platform</div>
                                <div class="text-xs sm:text-sm font-bold text-slate-900 capitalize">{{ $workshop->platform }} Live Online</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Banner Image Large -->
                <div class="aspect-video w-full overflow-hidden rounded-3xl border border-slate-200 bg-slate-100 shadow-sm">
                    <img 
                        src="{{ $workshop->banner_url }}" 
                        alt="{{ $workshop->title }}" 
                        class="h-full w-full object-cover"
                    />
                </div>

                <!-- Description / Silabus Content -->
                <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Silabus & Materi Workshop</h2>
                    <div class="prose max-w-none text-slate-700 text-sm sm:text-base leading-relaxed">
                        @php
                            $desc = (string) ($workshop->description ?? '');
                            $hasHtml = strip_tags($desc) !== $desc;
                        @endphp
                        @if($hasHtml)
                            {!! $desc !!}
                        @else
                            {!! \Illuminate\Support\Str::markdown($desc) !!}
                        @endif
                    </div>
                </div>

                <!-- Speaker Profile Card -->
                @if($workshop->speaker)
                    <div class="rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xs">
                        <h2 class="text-xl font-bold text-slate-900 mb-6">Instruktur / Pembicara</h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                            <img 
                                src="{{ $workshop->speaker->photo_url }}" 
                                alt="{{ $workshop->speaker->name }}" 
                                class="h-20 w-20 rounded-2xl object-cover border-2 border-indigo-200 shadow-sm"
                            />
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">{{ $workshop->speaker->name }}</h3>
                                <p class="text-xs sm:text-sm text-indigo-600 font-semibold">{{ $workshop->speaker->title_role }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $workshop->speaker->institution }}</p>
                                @if($workshop->speaker->bio)
                                    <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $workshop->speaker->bio }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- RIGHT 1 COLUMN: Sticky Checkout Card & Timer -->
            <div class="space-y-6">
                <div class="sticky top-24 rounded-3xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-200/60">
                    <!-- Countdown Timer -->
                    <div class="mb-6 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-4 text-center" 
                         x-data="countdownTimer('{{ $workshop->start_date->toISOString() }}')">
                        <div class="text-[11px] font-bold text-indigo-900 uppercase tracking-wider mb-2">Acara Dimulai Dalam:</div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            <div class="rounded-xl bg-white p-2 border border-slate-200 shadow-2xs">
                                <span class="text-xl font-bold text-slate-900" x-text="days">00</span>
                                <span class="block text-[10px] text-slate-500 font-medium">Hari</span>
                            </div>
                            <div class="rounded-xl bg-white p-2 border border-slate-200 shadow-2xs">
                                <span class="text-xl font-bold text-slate-900" x-text="hours">00</span>
                                <span class="block text-[10px] text-slate-500 font-medium">Jam</span>
                            </div>
                            <div class="rounded-xl bg-white p-2 border border-slate-200 shadow-2xs">
                                <span class="text-xl font-bold text-slate-900" x-text="minutes">00</span>
                                <span class="block text-[10px] text-slate-500 font-medium">Menit</span>
                            </div>
                            <div class="rounded-xl bg-white p-2 border border-slate-200 shadow-2xs">
                                <span class="text-xl font-bold text-slate-900" x-text="seconds">00</span>
                                <span class="block text-[10px] text-slate-500 font-medium">Detik</span>
                            </div>
                        </div>
                    </div>

                    <!-- Price Block -->
                    <div class="mb-6">
                        <div class="text-xs text-slate-500">Investasi Workshop:</div>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-900">
                                {{ $workshop->formattedPrice() }}
                            </span>
                            @if($workshop->formattedOriginalPrice())
                                <span class="text-sm text-slate-400 line-through">
                                    {{ $workshop->formattedOriginalPrice() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quota Bar -->
                    @if($workshop->quota)
                        <div class="mb-6 rounded-xl bg-slate-50 p-3.5 border border-slate-200">
                            <div class="flex justify-between text-xs text-slate-600 mb-1.5 font-medium">
                                <span>Sisa Kuota Kursi</span>
                                <span class="font-bold text-slate-900">{{ $workshop->remaining_quota }} dari {{ $workshop->quota }}</span>
                            </div>
                            @php
                                $pct = $workshop->quota > 0 ? round((($workshop->quota - $workshop->remaining_quota) / $workshop->quota) * 100) : 0;
                            @endphp
                            <div class="h-2 w-full rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Facility Checklist -->
                    <div class="mb-6 space-y-2.5 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Live Sesi Interaktif + Q&A</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>E-Sertifikat Resmi ber-QR Code</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Rekaman Video Full HD Akses Seumur Hidup</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Akses Materi Slide & Source Code Proyek</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    @if($isRegistered)
                        <div class="space-y-3">
                            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-center text-xs font-semibold text-emerald-800">
                                ✓ Anda sudah terdaftar untuk event ini
                            </div>
                            <a 
                                href="{{ route('participant.tickets') }}" 
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-6 py-3.5 text-sm font-bold text-white shadow-sm shadow-emerald-600/20 transition-all"
                            >
                                <span>Buka Tiket Saya</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    @elseif($workshop->quota !== null && $workshop->remaining_quota <= 0)
                        <button disabled class="w-full cursor-not-allowed rounded-xl bg-slate-100 px-6 py-3.5 text-sm font-bold text-slate-400 border border-slate-200">
                            Kuota Penuh (Sold Out)
                        </button>
                    @else
                        <button 
                            wire:click="openRegistrationModal" 
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-6 py-3.5 text-sm sm:text-base font-bold text-white shadow-sm shadow-indigo-600/25 transition-all"
                        >
                            <span>{{ $workshop->isFree() ? 'Ambil Tiket Gratis Sekarang' : 'Daftar & Bayar Sekarang' }}</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    @endif

                    <div class="mt-4 text-center text-[11px] text-slate-500 font-medium">
                        🔒 Pembayaran otomatis & instan via Midtrans (QRIS, VA, CC)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REGISTRATION MODAL -->
    @if($showRegisterModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-cloak>
            <div class="relative w-full max-w-lg rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-2xl animate-in fade-in zoom-in-95 duration-200">
                <!-- Close Button -->
                <button 
                    wire:click="$set('showRegisterModal', false)" 
                    class="absolute top-5 right-5 text-slate-400 hover:text-slate-600"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h3 class="text-xl font-bold text-slate-900">Konfirmasi Pendaftaran</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    {{ $workshop->title }}
                </p>

                <!-- Order summary -->
                <div class="my-5 rounded-2xl bg-slate-50 border border-slate-200 p-4 flex items-center justify-between text-xs sm:text-sm">
                    <span class="text-slate-600 font-medium">Total Biaya:</span>
                    <span class="text-base font-extrabold text-indigo-600">{{ $workshop->formattedPrice() }}</span>
                </div>

                @if($errorMessage)
                    <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Form Fields -->
                <form wire:submit.prevent="processRegistration" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap (Untuk Sertifikat) *</label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Contoh: Budi Pratama, S.Kom" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        />
                        @error('name') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email *</label>
                        <input 
                            type="email" 
                            wire:model="email" 
                            placeholder="nama@email.com" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        />
                        @error('email') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
                        <input 
                            type="text" 
                            wire:model="phone_number" 
                            placeholder="08xxxxxxxxxx" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        />
                        @error('phone_number') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Institusi / Kampus / Perusahaan</label>
                        <input 
                            type="text" 
                            wire:model="institution" 
                            placeholder="Universitas Indonesia / PT Maju Teknologi" 
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        />
                    </div>

                    @guest
                        <div class="rounded-xl bg-indigo-50 border border-indigo-200 p-3 text-xs text-indigo-900 flex items-start gap-2.5">
                            <svg class="h-4 w-4 text-indigo-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>
                                Akun portal peserta akan otomatis dibuatkan. Anda dapat login kembali menggunakan <strong>Email</strong> dan password default: <strong>Nomor WhatsApp</strong> Anda.
                            </span>
                        </div>
                    @endguest

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            wire:loading.attr="disabled"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-600/25 disabled:opacity-50 transition-all"
                        >
                            <span wire:loading.remove>
                                {{ $workshop->isFree() ? 'Konfirmasi Pendaftaran Gratis' : 'Lanjut ke Pembayaran Midtrans' }}
                            </span>
                            <span wire:loading class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                Sedang Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function countdownTimer(targetIso) {
        return {
            target: new Date(targetIso).getTime(),
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            timer: null,
            init() {
                this.update();
                this.timer = setInterval(() => this.update(), 1000);
            },
            update() {
                const now = new Date().getTime();
                const dist = this.target - now;
                if (dist <= 0) {
                    this.days = '00';
                    this.hours = '00';
                    this.minutes = '00';
                    this.seconds = '00';
                    clearInterval(this.timer);
                    return;
                }
                this.days = String(Math.floor(dist / (1000 * 60 * 60 * 24))).padStart(2, '0');
                this.hours = String(Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                this.minutes = String(Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                this.seconds = String(Math.floor((dist % (1000 * 60)) / 1000)).padStart(2, '0');
            }
        };
    }

    function workshopDetailHandler() {
        return {
            init() {
                window.addEventListener('open-midtrans-snap', (event) => {
                    const data = event.detail[0] || event.detail;
                    const token = data.token;
                    const orderCode = data.orderCode;

                    if (window.snap && typeof window.snap.pay === 'function') {
                        window.snap.pay(token, {
                            onSuccess: (result) => {
                                console.log('Payment success:', result);
                                @this.call('handlePaymentSuccess', orderCode);
                            },
                            onPending: (result) => {
                                console.log('Payment pending:', result);
                                alert('Transaksi Anda sedang diproses. Silakan selesaikan pembayaran sesuai petunjuk.');
                                @this.call('handlePaymentSuccess', orderCode);
                            },
                            onError: (result) => {
                                console.error('Payment error:', result);
                                alert('Pembayaran gagal atau dibatalkan.');
                            },
                            onClose: () => {
                                console.log('Snap modal closed by user');
                            }
                        });
                    } else {
                        // Fallback simulator for offline / dummy environment
                        if (confirm('Simulator Midtrans Snap Pop-up:\n\nOrder ' + orderCode + ' siap dibayar (Rp {{ number_format($workshop->price, 0, ",", ".") }}).\n\nKlik OK untuk mensimulasikan pembayaran BERHASIL via Midtrans.')) {
                            @this.call('handlePaymentSuccess', orderCode);
                        }
                    }
                });
            }
        };
    }
</script>
@endpush

@push('schema')
@php
    $schemaData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'EducationEvent',
                '@id' => route('workshop.detail', $workshop->slug) . '#event',
                'name' => $workshop->title,
                'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($workshop->short_description ?: $workshop->description)), 200),
                'startDate' => $workshop->start_date ? $workshop->start_date->toIso8601String() : now()->toIso8601String(),
                'endDate' => $workshop->end_date ? $workshop->end_date->toIso8601String() : ($workshop->start_date ? $workshop->start_date->addHours(3)->toIso8601String() : now()->addHours(3)->toIso8601String()),
                'eventAttendanceMode' => $workshop->platform === 'offline' ? 'https://schema.org/OfflineEventAttendanceMode' : 'https://schema.org/OnlineEventAttendanceMode',
                'eventStatus' => 'https://schema.org/EventScheduled',
                'location' => [
                    '@type' => $workshop->platform === 'offline' ? 'Place' : 'VirtualLocation',
                    'name' => $workshop->platform === 'offline' ? 'Gedung Workshop KuliahIlkom' : strtoupper($workshop->platform) . ' Meeting',
                    'url' => route('workshop.detail', $workshop->slug),
                ],
                'image' => [
                    $workshop->banner_url,
                ],
                'organizer' => [
                    '@type' => 'Organization',
                    'name' => 'KuliahIlkom',
                    'url' => 'https://workshop.kuliahilkom.com',
                    'logo' => asset('images/logo.png'),
                ],
                'performer' => [
                    '@type' => 'Person',
                    'name' => $workshop->speaker?->name ?? 'Instruktur KuliahIlkom',
                    'jobTitle' => $workshop->speaker?->title_role ?? 'Software Engineer',
                ],
                'offers' => [
                    '@type' => 'Offer',
                    'price' => $workshop->is_paid ? (float) $workshop->price : 0,
                    'priceCurrency' => 'IDR',
                    'url' => route('workshop.detail', $workshop->slug),
                    'validFrom' => $workshop->created_at ? $workshop->created_at->toIso8601String() : now()->toIso8601String(),
                    'availability' => $workshop->isSoldOut() ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
                ],
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => route('workshop.detail', $workshop->slug) . '#breadcrumb',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'Home',
                        'item' => route('home'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $workshop->category?->name ?? 'Workshop',
                        'item' => route('home') . '?category=' . $workshop->category?->slug,
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $workshop->title,
                        'item' => route('workshop.detail', $workshop->slug),
                    ],
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endpush
