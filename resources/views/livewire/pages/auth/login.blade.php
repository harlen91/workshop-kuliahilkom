<div class="flex min-h-[75vh] items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-3xl border border-slate-800 bg-slate-900/80 p-8 shadow-2xl backdrop-blur-xl">
        <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/25 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white">Masuk ke Akun Anda</h2>
            <p class="text-xs text-slate-400 mt-1">Akses tiket workshop, webinar, dan sertifikat Anda.</p>
        </div>

        @if($errorMessage)
            <div class="mt-6 rounded-xl bg-rose-500/10 border border-rose-500/30 p-3 text-xs text-rose-300">
                {{ $errorMessage }}
            </div>
        @endif

        <form wire:submit.prevent="login" class="mt-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email</label>
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="nama@email.com" 
                    class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none"
                    required
                />
                @error('email') <span class="text-[11px] text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-300">Password</label>
                </div>
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••" 
                    class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-xs sm:text-sm text-white placeholder-slate-500 focus:border-indigo-500 focus:outline-none"
                    required
                />
                @error('password') <span class="text-[11px] text-rose-400 mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-slate-400">
                    <input type="checkbox" wire:model="remember" class="rounded border-slate-800 bg-slate-950 text-indigo-600 focus:ring-0">
                    <span>Ingat saya</span>
                </label>
            </div>

            <button 
                type="submit" 
                class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-600/30 hover:from-indigo-400 hover:to-indigo-500 transition-all"
            >
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-400">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300">Daftar Akun Baru</a>
        </div>
    </div>
</div>
