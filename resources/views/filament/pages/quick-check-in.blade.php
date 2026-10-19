<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Event Filter Bar -->
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex-1">
                <label for="workshop_select" class="block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-300 mb-1">
                    Pilih Event Workshop / Webinar:
                </label>
                <select id="workshop_select" wire:model.live="selectedWorkshopId" class="w-full text-sm font-semibold rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Event Aktif</option>
                    @foreach($this->workshops as $workshop)
                        <option value="{{ $workshop->id }}">
                            {{ $workshop->title }} ({{ $workshop->start_date ? $workshop->start_date->format('d M Y') : 'TBA' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Mode scan aktif. Kompatibel dengan alat barcode scanner USB/Bluetooth & input manual keyboard.
            </div>
        </div>

        <!-- Live Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Total Terdaftar</span>
                <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ number_format($this->workshopStats['total']) }}</p>
                <span class="text-xs text-gray-500">Tiket terbit</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-green-200 dark:border-green-800/40 bg-green-50/20 shadow-sm">
                <span class="text-xs font-semibold text-green-700 dark:text-green-400 uppercase">Sudah Hadir</span>
                <p class="text-2xl font-black text-green-600 dark:text-green-400 mt-1">{{ number_format($this->workshopStats['attended']) }}</p>
                <span class="text-xs text-green-600 dark:text-green-400 font-medium">{{ $this->workshopStats['percentage'] }}% Hadir</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-amber-200 dark:border-amber-800/40 bg-amber-50/20 shadow-sm">
                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 uppercase">Belum Hadir</span>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($this->workshopStats['remaining']) }}</p>
                <span class="text-xs text-amber-600 dark:text-amber-400">Menunggu check-in</span>
            </div>
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-indigo-200 dark:border-indigo-800/40 bg-indigo-50/20 shadow-sm">
                <span class="text-xs font-semibold text-indigo-700 dark:text-indigo-400 uppercase">Rasio Kehadiran</span>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $this->workshopStats['percentage'] }}%</p>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $this->workshopStats['percentage']) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Check-In Scanner Box -->
        <div class="bg-gradient-to-r from-indigo-900 to-slate-900 text-white p-6 rounded-2xl shadow-lg">
            <div class="max-w-2xl mx-auto text-center space-y-4">
                <div class="inline-flex items-center justify-center p-3 bg-indigo-500/20 border border-indigo-400/30 rounded-2xl">
                    <x-heroicon-o-qr-code class="w-8 h-8 text-indigo-300" />
                </div>
                <h2 class="text-xl font-bold tracking-tight">Scan Barcode / Masukkan Kode Tiket</h2>
                <p class="text-xs text-indigo-200 max-w-md mx-auto">
                    Arahkan scanner ke QR Code peserta atau ketik kode tiket (misal: <code class="bg-indigo-800/60 px-2 py-0.5 rounded text-white font-mono">TKT-xxxx</code>) lalu tekan <strong>Enter</strong>.
                </p>

                <form wire:submit.prevent="processCheckIn" class="flex flex-col sm:flex-row gap-2 justify-center max-w-lg mx-auto pt-2">
                    <input 
                        type="text" 
                        wire:model="ticketCode" 
                        autofocus
                        placeholder="Scan / Ketik TKT-..." 
                        class="flex-1 px-4 py-3 text-base font-mono font-bold uppercase rounded-xl bg-white text-gray-900 placeholder-gray-400 border-2 border-transparent focus:border-indigo-400 focus:outline-none shadow-inner"
                    />
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-md flex items-center justify-center gap-2"
                    >
                        <x-heroicon-m-check-badge class="w-5 h-5" />
                        <span>Verifikasi</span>
                    </button>
                </form>

                <div wire:loading wire:target="processCheckIn" class="text-xs text-indigo-300 animate-pulse font-mono">
                    Sedang memverifikasi data tiket...
                </div>
            </div>
        </div>

        <!-- Scan Result Card Feedback -->
        @if($scanStatus === 'success' && $lastCheckedIn)
            <div class="bg-green-50 dark:bg-green-950/40 border border-green-300 dark:border-green-800 rounded-xl p-5 shadow-sm animate-fade-in">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-green-500 text-white rounded-xl">
                        <x-heroicon-m-check-circle class="w-8 h-8" />
                    </div>
                    <div class="flex-1">
                        <span class="text-xs font-bold text-green-700 dark:text-green-400 uppercase tracking-wider">Presensi Berhasil Diverifikasi!</span>
                        <h3 class="text-lg font-bold text-green-900 dark:text-green-200 mt-0.5">{{ $lastCheckedIn['name'] }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-2 text-xs text-green-800 dark:text-green-300">
                            <div><strong>Kode Tiket:</strong> <span class="font-mono">{{ $lastCheckedIn['ticket_code'] }}</span></div>
                            <div><strong>WhatsApp:</strong> {{ $lastCheckedIn['phone'] ?: '-' }}</div>
                            <div><strong>Institusi:</strong> {{ $lastCheckedIn['institution'] ?: '-' }}</div>
                        </div>
                        <div class="mt-2 text-xs text-green-700 dark:text-green-400">
                            Event: <strong>{{ $lastCheckedIn['workshop'] }}</strong> &bull; Waktu Check-in: <strong>{{ $lastCheckedIn['attended_at'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($scanStatus === 'already_attended' && $lastCheckedIn)
            <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 rounded-xl p-5 shadow-sm animate-fade-in">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-amber-500 text-white rounded-xl">
                        <x-heroicon-m-exclamation-triangle class="w-8 h-8" />
                    </div>
                    <div class="flex-1">
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">Tiket Sudah Pernah Digunakan!</span>
                        <h3 class="text-lg font-bold text-amber-900 dark:text-amber-200 mt-0.5">{{ $lastCheckedIn['name'] }}</h3>
                        <p class="text-xs text-amber-800 dark:text-amber-300 mt-1">{{ $scanMessage }}</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2 text-xs text-amber-800 dark:text-amber-300">
                            <div><strong>Kode Tiket:</strong> <span class="font-mono">{{ $lastCheckedIn['ticket_code'] }}</span></div>
                            <div><strong>Event:</strong> {{ $lastCheckedIn['workshop'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($scanStatus === 'not_found')
            <div class="bg-red-50 dark:bg-red-950/40 border border-red-300 dark:border-red-800 rounded-xl p-5 shadow-sm animate-fade-in flex items-center gap-4">
                <div class="p-2 bg-red-500 text-white rounded-xl">
                    <x-heroicon-m-x-circle class="w-8 h-8" />
                </div>
                <div>
                    <span class="text-xs font-bold text-red-700 dark:text-red-400 uppercase tracking-wider">Gagal Verifikasi</span>
                    <p class="text-sm font-semibold text-red-900 dark:text-red-200 mt-0.5">{{ $scanMessage }}</p>
                    <p class="text-xs text-red-700 dark:text-red-400 mt-1">Pastikan event yang dipilih sesuai dan tiket sudah berstatus lunas.</p>
                </div>
            </div>
        @endif

        <!-- Recent Check-Ins Live Feed -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-clock class="w-4 h-4 text-gray-500" />
                    <span>Daftar Peserta Baru Check-In (Live Feed)</span>
                </h3>
                <span class="text-xs text-gray-500">6 Terakhir</span>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($this->recentCheckIns as $reg)
                    <div class="p-3.5 hover:bg-gray-50 dark:hover:bg-gray-750 flex items-center justify-between gap-3 text-sm">
                        <div class="flex items-center gap-3">
                            <img 
                                src="{{ $reg->user?->avatar_url }}" 
                                alt="{{ $reg->user?->name }}" 
                                class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-700" 
                            />
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-xs">{{ $reg->user?->name }}</h4>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 font-mono">{{ $reg->ticket_code }} &bull; {{ $reg->workshop?->title }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                HADIR
                            </span>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $reg->attended_at ? $reg->attended_at->diffForHumans() : '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-gray-500">
                        Belum ada peserta yang melakukan presensi pada event ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-filament-panels::page>
