<div class="flex min-h-[75vh] items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-lg rounded-3xl border border-slate-200/90 bg-white p-8 shadow-xl shadow-slate-200/50">
        <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-md shadow-indigo-600/20 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Daftar Akun Peserta Baru</h2>
            <p class="text-xs text-slate-500 mt-1">Satu akun untuk mengikuti seluruh workshop, webinar, dan sertifikat.</p>
        </div>

        <form wire:submit.prevent="register" class="mt-6 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap (Sesuai Sertifikat) *</label>
                <input 
                    type="text" 
                    wire:model="name" 
                    placeholder="Nama Lengkap Anda" 
                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                    required
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
                    required
                />
                @error('email') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
                    <input 
                        type="text" 
                        wire:model="phone_number" 
                        placeholder="08xxxxxxxxxx" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        required
                    />
                    @error('phone_number') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Institusi / Kampus / Kantor</label>
                    <input 
                        type="text" 
                        wire:model="institution" 
                        placeholder="Contoh: Univ Indonesia / Freelancer" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password *</label>
                    <input 
                        type="password" 
                        wire:model="password" 
                        placeholder="Min. 6 karakter" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        required
                    />
                    @error('password') <span class="text-[11px] text-rose-500 mt-0.5 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Password *</label>
                    <input 
                        type="password" 
                        wire:model="password_confirmation" 
                        placeholder="Ketik ulang password" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all"
                        required
                    />
                </div>
            </div>

            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-700 px-5 py-3 text-sm font-bold text-white shadow-sm shadow-indigo-600/25 transition-all"
                >
                    Daftar Akun Sekarang
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">Masuk di sini</a>
        </div>
    </div>
</div>
