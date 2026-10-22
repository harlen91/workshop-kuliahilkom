<div class="flex min-h-[75vh] items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-3xl border border-slate-200/90 bg-white p-8 shadow-xl shadow-slate-200/50">
        <div class="text-center">
            <div
                class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-md shadow-indigo-600/20 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Masuk ke Akun Anda</h2>
            <p class="text-xs text-slate-500 mt-1">Akses tiket workshop, webinar, dan sertifikat Anda.</p>
        </div>

        @if ($errorMessage)
            <div class="mt-6 rounded-xl bg-rose-50 border border-rose-200 p-3 text-xs text-rose-700">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="mt-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" wire:model="email" placeholder="nama@email.com"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                    required />
                @error('email')
                    <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700">Password</label>
                </div>
                <input type="password" wire:model="password" placeholder="••••••••"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                    required />
                @error('password')
                    <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span>
                @enderror

                <div
                    class="mt-2 rounded-xl bg-indigo-50/80 border border-indigo-100 p-2.5 text-[11px] text-indigo-900 flex items-start gap-2">
                    <svg class="h-4 w-4 text-indigo-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        Mendaftar langsung via form workshop? Password default Anda adalah <strong>Nomor WhatsApp</strong> yang Anda daftarkan.
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" wire:model="remember"
                        class="rounded border-slate-300 bg-white text-indigo-600 focus:ring-0">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button type="submit"
                class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-600/25 transition-all">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Belum memiliki akun?
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Daftar Akun
                Baru</a>
        </div>
    </div>
</div>
