<div class="py-10">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <!-- Flash Alert -->
        @if(session()->has('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800 flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-xs sm:text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session()->has('account_created_notice'))
            @php $accNotice = session('account_created_notice'); @endphp
            <div x-data="{ show: true }" x-show="show" x-transition class="mb-8 overflow-hidden rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 via-white to-purple-50 p-5 sm:p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-600/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-900">
                                Informasi Akun Login Portal Peserta
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                                Akun portal Anda otomatis dibuatkan. Simpan kredensial ini untuk login kembali ke portal di lain waktu atau lewat perangkat lain:
                            </p>

                            <div class="mt-3.5 flex flex-wrap items-center gap-2.5 text-xs sm:text-sm">
                                <div class="inline-flex items-center gap-1.5 rounded-xl bg-white border border-slate-200 px-3 py-1.5 shadow-2xs">
                                    <span class="text-slate-500 font-medium">Email:</span>
                                    <strong class="font-semibold text-slate-900">{{ $accNotice['email'] ?? auth()->user()?->email }}</strong>
                                </div>
                                <div class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-100/70 border border-indigo-200 px-3 py-1.5 text-indigo-950 shadow-2xs">
                                    <span class="text-indigo-700 font-medium">Password Default:</span>
                                    <strong class="font-mono font-bold text-indigo-900">{{ $accNotice['phone'] ?? auth()->user()?->phone_number }}</strong>
                                    <span class="text-[11px] text-indigo-600 font-normal">(Nomor WhatsApp)</span>
                                </div>
                            </div>

                            <p class="mt-2.5 text-[11px] text-slate-500">
                                💡 <em>Tips: Gunakan kombinasi Email dan Nomor WhatsApp di atas jika Anda ingin masuk kembali melalui halaman Login.</em>
                            </p>
                        </div>
                    </div>
                    <button 
                        type="button"
                        @click="show = false" 
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors shrink-0"
                        title="Tutup informasi"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Tiket & Event Saya</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Kelola tiket pendaftaran, link ruang virtual, materi, dan e-sertifikat Anda.
                </p>
            </div>

            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 hover:border-indigo-300 hover:text-indigo-600 shadow-2xs transition-colors">
                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Jelajahi Workshop Lain
            </a>
        </div>

        <!-- Tabs -->
        <div class="mt-6 flex items-center gap-3">
            <button 
                wire:click="setTab('upcoming')" 
                class="rounded-xl px-4 py-2 text-xs sm:text-sm font-semibold transition-all {{ $activeTab === 'upcoming' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                Event Mendatang
            </button>
            <button 
                wire:click="setTab('past')" 
                class="rounded-xl px-4 py-2 text-xs sm:text-sm font-semibold transition-all {{ $activeTab === 'past' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}"
            >
                Riwayat Selesai
            </button>
        </div>

        <!-- Ticket List -->
        <div class="mt-8 space-y-6">
            @if($registrations->isEmpty())
                <div class="rounded-3xl border border-slate-200 bg-white p-12 text-center shadow-xs">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 mb-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Belum Ada Tiket di Kategori Ini</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Anda belum mendaftar di workshop {{ $activeTab === 'upcoming' ? 'mendatang' : 'sebelumnya' }}. Temukan event menarik untuk upgrade skill Anda hari ini!
                    </p>
                    <a href="{{ route('home') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                        Lihat Katalog Workshop
                    </a>
                </div>
            @else
                @foreach($registrations as $reg)
                    @php $ws = $reg->workshop; @endphp
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-xs transition-all hover:border-indigo-300 hover:shadow-lg hover:shadow-slate-200/60">
                        <div class="grid grid-cols-1 md:grid-cols-3">
                            <!-- Left 2 Cols: Event Info & Protected Access -->
                            <div class="p-6 sm:p-8 md:col-span-2 flex flex-col justify-between space-y-6">
                                <div>
                                    <!-- Badges & Status -->
                                    <div class="flex flex-wrap items-center gap-2 mb-3">
                                        <span class="rounded-lg px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider {{ $ws->type === 'workshop' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                            {{ $ws->type }}
                                        </span>
                                        <span class="rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-semibold">
                                            ✓ Terkonfirmasi (Lunas)
                                        </span>
                                        <span class="rounded-lg bg-slate-100 text-slate-700 px-2.5 py-0.5 text-[11px] font-medium">
                                            {{ $ws->category?->name ?? 'General' }}
                                        </span>
                                    </div>

                                    <!-- Title -->
                                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 hover:text-indigo-600 transition-colors">
                                        <a href="{{ route('workshop.detail', $ws->slug) }}">
                                            {{ $ws->title }}
                                        </a>
                                    </h2>

                                    <!-- Speaker & Schedule Meta -->
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="font-medium">{{ $ws->start_date->isoFormat('dddd, D MMMM Y') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="font-medium">{{ $ws->start_date->format('H:i') }} - {{ $ws->end_date->format('H:i') }} {{ $ws->timezone }}</span>
                                        </div>
                                        @if($ws->speaker)
                                            <div class="flex items-center gap-2 sm:col-span-2">
                                                <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                <span>Instruktur: <strong class="text-slate-900">{{ $ws->speaker->name }}</strong> ({{ $ws->speaker->title_role }})</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Protected Access Room Box -->
                                <div class="rounded-2xl border border-indigo-200/80 bg-indigo-50/50 p-4 sm:p-5">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-900">
                                            <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-ping"></span>
                                            Akses Ruang Virtual (Khusus Peserta)
                                        </div>
                                        <span class="text-[11px] text-slate-500 capitalize font-medium">Platform: {{ $ws->platform }}</span>
                                    </div>

                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                        @if($ws->meeting_url)
                                            @if($ws->isJoinRoomAttendance() && ! $reg->isAttended())
                                                <button 
                                                    type="button"
                                                    wire:click="joinRoomAndCheckIn({{ $reg->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="joinRoomAndCheckIn({{ $reg->id }})"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm shadow-indigo-600/25 transition-all"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <span wire:loading.remove wire:target="joinRoomAndCheckIn({{ $reg->id }})">Masuk ke Room & Presensi</span>
                                                    <span wire:loading wire:target="joinRoomAndCheckIn({{ $reg->id }})">Membuka Room...</span>
                                                </button>
                                            @else
                                                <a 
                                                    href="{{ $ws->meeting_url }}" 
                                                    target="_blank" 
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm shadow-indigo-600/25 transition-all"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                    </svg>
                                                    <span>Masuk ke Room {{ ucfirst($ws->platform) }}</span>
                                                </a>
                                            @endif
                                        @else
                                            <div class="inline-flex items-center gap-2 rounded-xl bg-amber-50 border border-amber-200 px-4 py-2.5 text-xs font-semibold text-amber-800">
                                                <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <span>Link room {{ ucfirst($ws->platform) }} belum disematkan / akan dibagikan menjelang acara</span>
                                            </div>
                                        @endif

                                        @if($ws->meeting_password)
                                            <div class="flex items-center justify-between rounded-xl bg-white border border-slate-200 px-3.5 py-2 text-xs shadow-2xs" x-data="{ copied: false }">
                                                <span class="text-slate-600 mr-2">Passcode: <strong class="text-slate-900 font-mono">{{ $ws->meeting_password }}</strong></span>
                                                <button 
                                                    @click="navigator.clipboard.writeText('{{ $ws->meeting_password }}'); copied = true; setTimeout(() => copied = false, 2000)" 
                                                    class="text-indigo-600 hover:text-indigo-700 font-semibold"
                                                >
                                                    <span x-show="!copied">Salin</span>
                                                    <span x-show="copied" class="text-emerald-600">Tersalin!</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Post Event Resources (Recording & Certificate) -->
                                @if($reg->certificate_code || $ws->recording_url || $ws->materials_url)
                                    <div class="border-t border-slate-100 pt-3 flex flex-wrap items-center gap-3 text-xs">
                                        @if($reg->certificate_code)
                                            <div class="rounded-lg bg-purple-50 border border-purple-200 px-3 py-1.5 text-purple-700 flex items-center gap-1.5 font-medium">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138z" />
                                                </svg>
                                                <span>No. Sertifikat: <strong>{{ $reg->certificate_code }}</strong></span>
                                            </div>
                                        @endif

                                        @if($ws->recording_url)
                                            <a href="{{ $ws->recording_url }}" target="_blank" class="rounded-lg bg-pink-50 border border-pink-200 px-3 py-1.5 text-pink-700 hover:bg-pink-100 transition-colors font-medium">
                                                🎬 Tonton Rekaman Kelas
                                            </a>
                                        @endif

                                        @if($ws->materials_url)
                                            <a href="{{ $ws->materials_url }}" target="_blank" class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-1.5 text-slate-700 hover:bg-slate-100 transition-colors font-medium">
                                                📁 Unduh Bahan & Slide
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Right 1 Col: Digital Ticket Stub with Attendance Action -->
                            <div class="border-t md:border-t-0 md:border-l border-slate-200 bg-slate-50/80 p-5 sm:p-6 flex flex-col justify-between text-center relative" x-data="{ showQr: {{ $ws->isQrAttendance() ? 'true' : 'false' }} }">
                                <div class="w-full">
                                    <div class="text-[10px] uppercase font-bold tracking-widest text-slate-500 mb-1">Tiket Masuk Digital</div>
                                    <div class="font-mono text-sm font-extrabold text-slate-900 bg-white border border-slate-200 rounded-lg py-1 px-3 inline-block shadow-2xs">
                                        {{ $reg->ticket_code }}
                                    </div>

                                    @if($reg->isAttended())
                                        <!-- State: Already Attended -->
                                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4 text-center">
                                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-xs">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <div class="mt-2 text-xs font-bold text-emerald-900">Kehadiran Terverifikasi</div>
                                            <div class="mt-0.5 text-[11px] text-emerald-700">
                                                {{ $reg->attended_at?->isoFormat('D MMM Y, HH:mm') ?? now()->isoFormat('D MMM Y, HH:mm') }} WIB
                                            </div>
                                            @if($reg->certificate_code)
                                                <div class="mt-2.5 pt-2 border-t border-emerald-200/70 text-[10px] font-mono font-medium text-emerald-800 truncate" title="{{ $reg->certificate_code }}">
                                                    ID: {{ $reg->certificate_code }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <!-- State: Pending Attendance -->
                                        @if($ws->isTokenAttendance())
                                            <!-- Mode A: Token Sesi -->
                                            <div class="mt-4 rounded-2xl border border-indigo-100 bg-white p-3.5 shadow-2xs text-left">
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900">
                                                    <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                    </svg>
                                                    <span>Klaim Presensi Sesi</span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                                    Masukkan kode token yang dibagikan pemateri di sesi webinar:
                                                </p>

                                                @if($ws->isAttendanceOpen())
                                                    <div class="mt-2.5 flex items-center gap-1.5">
                                                        <input 
                                                            type="text" 
                                                            wire:model="inputTokens.{{ $reg->id }}"
                                                            wire:keydown.enter="claimAttendance({{ $reg->id }})"
                                                            placeholder="Ketik Token..." 
                                                            class="w-full uppercase tracking-wider font-mono text-xs font-bold rounded-xl border border-slate-300 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder:normal-case placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400"
                                                        >
                                                        <button 
                                                            type="button"
                                                            wire:click="claimAttendance({{ $reg->id }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="claimAttendance({{ $reg->id }})"
                                                            class="shrink-0 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 px-3.5 py-2 text-xs font-bold text-white shadow-2xs transition-colors"
                                                        >
                                                            <span wire:loading.remove wire:target="claimAttendance({{ $reg->id }})">Klaim</span>
                                                            <span wire:loading wire:target="claimAttendance({{ $reg->id }})">...</span>
                                                        </button>
                                                    </div>
                                                @else
                                                    <div class="mt-2 rounded-xl bg-amber-50 border border-amber-200 p-2 text-[11px] text-amber-800 font-medium">
                                                        ⏳ Gerbang presensi belum dibuka oleh instruktur.
                                                    </div>
                                                @endif
                                            </div>

                                        @elseif($ws->isJoinRoomAttendance())
                                            <!-- Mode B: Auto-Presensi via Join Room -->
                                            <div class="mt-4 rounded-2xl border border-indigo-100 bg-white p-3.5 shadow-2xs text-left">
                                                <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-950">
                                                    <svg class="h-4 w-4 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Presensi Otomatis</span>
                                                </div>
                                                <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">
                                                    Kehadiran Anda akan dicatat otomatis saat mengklik tombol Masuk ke Room.
                                                </p>
                                                @if($ws->meeting_url)
                                                    <button 
                                                        type="button"
                                                        wire:click="joinRoomAndCheckIn({{ $reg->id }})"
                                                        class="mt-2.5 w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 px-3 py-2 text-xs font-bold text-white shadow-2xs transition-colors"
                                                    >
                                                        <span>Presensi Sekarang</span>
                                                    </button>
                                                @endif
                                            </div>

                                        @else
                                            <!-- Mode C: Scan QR Code (Default / Offline) -->
                                            <!-- Handled below by QR Display -->
                                        @endif
                                    @endif

                                    <!-- Collapsible QR Code Graphic -->
                                    <div x-show="showQr" x-collapse class="mt-3">
                                        <div class="mx-auto w-32 h-32 bg-white p-2 rounded-2xl shadow-2xs border border-slate-200 flex items-center justify-center overflow-hidden">
                                            @if($reg->qr_code_svg)
                                                <div class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                                                    {!! $reg->qr_code_svg !!}
                                                </div>
                                            @else
                                                <div class="text-xs text-slate-400 font-mono text-center p-2">
                                                    {{ $reg->ticket_code }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-1 font-mono">Barcode Verifikasi Tiket</div>
                                    </div>

                                    @if(! $ws->isQrAttendance())
                                        <button 
                                            type="button" 
                                            @click="showQr = !showQr" 
                                            class="mt-3 text-[11px] font-semibold text-slate-500 hover:text-indigo-600 transition-colors inline-flex items-center gap-1"
                                        >
                                            <span x-text="showQr ? 'Tutup Barcode QR' : 'Lihat Barcode Tiket (QR)'"></span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Attendance Status Footer -->
                                <div class="mt-4 pt-3 border-t border-slate-200/80 w-full text-xs">
                                    <div class="text-slate-500">Status Kehadiran:</div>
                                    @if($reg->isAttended())
                                        <div class="text-emerald-600 font-bold mt-0.5">✓ Sudah Hadir</div>
                                    @else
                                        <div class="text-amber-600 font-semibold mt-0.5">
                                            @if($ws->isTokenAttendance())
                                                Menunggu Klaim Token
                                            @elseif($ws->isJoinRoomAttendance())
                                                Belum Masuk Room
                                            @else
                                                Belum Hadir (Hari H)
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
