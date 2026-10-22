<x-filament-panels::page>
    @php
        $storage = $this->getStorageStatus();
        $sysInfo = $this->getSystemInfo();
    @endphp

    <!-- System Status & Health Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Storage Link Status -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Storage Link</span>
                @if($storage['is_valid'])
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Terhubung
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 dark:bg-rose-500/10 px-2 py-0.5 text-xs font-semibold text-rose-600 dark:text-rose-400">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        Terputus
                    </span>
                @endif
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100 truncate" title="{{ $storage['target'] }}">
                {{ basename($storage['target']) }}
            </p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                {{ $storage['message'] }}
            </p>
        </div>

        <!-- Database Connection -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Database</span>
                <span class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase">
                    {{ $sysInfo['db_connection'] }}
                </span>
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                {{ $sysInfo['db_name'] }}
            </p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                Host: {{ $sysInfo['db_host'] }}
            </p>
        </div>

        <!-- Environment -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Environment</span>
                <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-500/10 px-2 py-0.5 text-xs font-semibold text-purple-600 dark:text-purple-400">
                    {{ strtoupper($sysInfo['app_env']) }}
                </span>
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                Laravel v{{ $sysInfo['laravel_version'] }}
            </p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                Default Disk: {{ $sysInfo['storage_disk'] }}
            </p>
        </div>

        <!-- PHP Version -->
        <div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">PHP Runtime</span>
                <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-500/10 px-2 py-0.5 text-xs font-semibold text-blue-600 dark:text-blue-400">
                    Active
                </span>
            </div>
            <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                PHP {{ $sysInfo['php_version'] }}
            </p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                Server OS: {{ PHP_OS_FAMILY }}
            </p>
        </div>
    </div>

    <!-- Maintenance Action Panels -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Panel 1: Database Migrations -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <x-heroicon-o-circle-stack class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Migrasi Database</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kelola skema & update tabel</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    Jalankan migrasi untuk menambahkan tabel/kolom baru tanpa merusak data yang telah ada.
                </p>
            </div>

            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                <x-filament::button 
                    color="gray" 
                    icon="heroicon-o-clipboard-document-list" 
                    wire:click="runMigrationStatus" 
                    class="w-full"
                >
                    Cek Status Migrasi
                </x-filament::button>

                <x-filament::button 
                    color="primary" 
                    icon="heroicon-o-arrow-path" 
                    wire:click="runMigration" 
                    wire:confirm="Apakah Anda yakin ingin mengeksekusi migrasi database (php artisan migrate --force)?" 
                    class="w-full"
                >
                    Jalankan Migrasi Database
                </x-filament::button>

                <x-filament::button 
                    color="warning" 
                    icon="heroicon-o-document-plus" 
                    wire:click="runDatabaseSeed" 
                    wire:confirm="PERINGATAN: Menjalankan seeder akan mengisi data awal/sample ke database. Lanjutkan eksekusi db:seed --force?" 
                    class="w-full"
                >
                    Jalankan Database Seeder
                </x-filament::button>
            </div>
        </div>

        <!-- Panel 2: Storage Link Management -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
                        <x-heroicon-o-photo class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Storage Symlink</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tautan berkas gambar publik</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    Menautkan folder <code>public/storage</code> ke <code>storage/app/public</code> agar banner dan avatar dapat diakses publik.
                </p>
            </div>

            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                <x-filament::button 
                    color="primary" 
                    icon="heroicon-o-link" 
                    wire:click="runStorageLink" 
                    class="w-full"
                >
                    Hubungkan Storage Link
                </x-filament::button>

                <x-filament::button 
                    color="gray" 
                    icon="heroicon-o-wrench-screwdriver" 
                    wire:click="runStorageRelink" 
                    wire:confirm="Symlink lama akan dihapus dan dibuat tautan baru ke storage/app/public. Lanjutkan?" 
                    class="w-full"
                >
                    Perbaiki / Buat Ulang Link
                </x-filament::button>
            </div>
        </div>

        <!-- Panel 3: Cache & Performance -->
        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <x-heroicon-o-bolt class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Cache & Optimasi</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Penyegaran performa aplikasi</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    Bersihkan cache saat ada perubahan konfigurasi atau aktifkan caching untuk performa maksimal.
                </p>
            </div>

            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                <x-filament::button 
                    color="danger" 
                    icon="heroicon-o-trash" 
                    wire:click="runOptimizeClear" 
                    class="w-full"
                >
                    Bersihkan Seluruh Cache
                </x-filament::button>

                <x-filament::button 
                    color="success" 
                    icon="heroicon-o-rocket-launch" 
                    wire:click="runOptimize" 
                    class="w-full"
                >
                    Optimalkan Aplikasi
                </x-filament::button>
            </div>
        </div>
    </div>

    <!-- Loading Indicator Banner -->
    <div 
        wire:loading.flex 
        wire:target="runMigration, runMigrationStatus, runDatabaseSeed, runStorageLink, runStorageRelink, runOptimizeClear, runOptimize"
        class="rounded-xl border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-950/60 p-4 items-center gap-3 text-indigo-700 dark:text-indigo-300 shadow-sm"
    >
        <svg class="animate-spin h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span class="text-xs sm:text-sm font-medium">Sedang mengeksekusi perintah Artisan di server... Mohon tunggu.</span>
    </div>

    <!-- Terminal Console Output Window -->
    <div class="rounded-2xl border border-gray-800 bg-slate-950 overflow-hidden shadow-2xl">
        <!-- Terminal Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-slate-900/90 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-rose-500/80 inline-block"></span>
                <span class="h-3 w-3 rounded-full bg-amber-500/80 inline-block"></span>
                <span class="h-3 w-3 rounded-full bg-emerald-500/80 inline-block"></span>
                <span class="ml-2 font-mono text-xs text-slate-400 font-semibold">
                    {{ $lastCommand ? '$ '.$lastCommand : 'Terminal Log Output' }}
                </span>
            </div>

            <div class="flex items-center gap-3">
                @if($executedAt)
                    <span class="text-[11px] font-mono text-slate-500 hidden sm:inline-block">
                        {{ $executedAt }}
                    </span>
                    <span class="inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-mono font-bold {{ $isSuccess ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }}">
                        {{ $isSuccess ? 'EXIT 0' : 'EXIT 1' }}
                    </span>
                @endif

                @if($commandOutput)
                    <button 
                        type="button" 
                        wire:click="clearOutput" 
                        class="text-xs text-slate-400 hover:text-white transition-colors"
                        title="Bersihkan log output"
                    >
                        <x-heroicon-o-x-mark class="h-4 w-4" />
                    </button>
                @endif
            </div>
        </div>

        <!-- Terminal Body -->
        <div class="p-4 font-mono text-xs text-emerald-400 bg-slate-950 min-h-[160px] max-h-[420px] overflow-y-auto whitespace-pre-wrap select-all">
@if($commandOutput){{ $commandOutput }}@else<span class="text-slate-600">// Tidak ada perintah yang baru dijalankan. Silakan pilih salah satu aksi di atas untuk melihat log eksekusi.</span>@endif
        </div>
    </div>
</x-filament-panels::page>
