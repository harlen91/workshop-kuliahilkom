@component('layouts.app', [
    'metaTitle' => 'Verifikasi E-Sertifikat - KuliahIlkom Workshop',
    'metaDescription' => 'Verifikasi keaslian E-Sertifikat resmi peserta workshop dan webinar KuliahIlkom.',
    'metaRobots' => 'noindex, follow',
])
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="text-center">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            Verifikasi Resmi KuliahIlkom
        </span>
        <h1 class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
            Verifikasi Keaslian E-Sertifikat
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            Masukkan nomor sertifikat yang tertera pada dokumen PDF untuk memeriksa keabsahan data peserta dan program.
        </p>
    </div>

    <form method="GET" action="{{ route('certificates.verify') }}" class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs sm:p-6">
        <label for="certificate-code" class="block text-xs font-semibold uppercase tracking-wider text-slate-500">
            Nomor Sertifikat
        </label>
        <div class="mt-2 flex flex-col gap-3 sm:flex-row">
            <input
                id="certificate-code"
                type="text"
                name="code"
                value="{{ $code }}"
                placeholder="Contoh: CERT-20261125-ABC123"
                required
                class="w-full flex-1 rounded-xl border border-slate-300 bg-slate-50 px-4 py-2.5 font-mono text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
            />
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-indigo-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Verifikasi Sertifikat
            </button>
        </div>
    </form>

    @if($code !== '')
        @if($isValid && $registration)
            <div class="mt-6 overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm">
                <div class="border-b border-emerald-200 bg-emerald-50 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-emerald-950">Sertifikat Terverifikasi &amp; Sah</div>
                            <div class="text-xs text-emerald-700">Data tercatat resmi dalam sistem KuliahIlkom Workshop.</div>
                        </div>
                    </div>
                    <span class="rounded-lg border border-emerald-300 bg-white px-3 py-1 font-mono text-xs font-bold text-emerald-800">
                        {{ $registration->certificate_code }}
                    </span>
                </div>

                <dl class="divide-y divide-slate-100 px-6 py-2 text-sm">
                    <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                        <dt class="font-medium text-slate-500">Nama Penerima</dt>
                        <dd class="font-bold text-slate-900 sm:col-span-2">{{ $registration->user?->name }}</dd>
                    </div>
                    @if($registration->user?->institution)
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-medium text-slate-500">Institusi / Afiliasi</dt>
                            <dd class="font-medium text-slate-800 sm:col-span-2">{{ $registration->user->institution }}</dd>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                        <dt class="font-medium text-slate-500">Program / Event</dt>
                        <dd class="font-semibold text-indigo-700 sm:col-span-2">
                            {{ $registration->workshop?->title }}
                            @if($registration->workshop?->type)
                                <span class="ml-1.5 inline-block rounded-md bg-indigo-50 px-2 py-0.5 text-[11px] font-semibold uppercase text-indigo-700">
                                    {{ $registration->workshop->type }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if($registration->workshop?->category)
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-medium text-slate-500">Kategori</dt>
                            <dd class="text-slate-800 sm:col-span-2">{{ $registration->workshop->category->name }}</dd>
                        </div>
                    @endif
                    @if($registration->workshop?->speaker)
                        <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                            <dt class="font-medium text-slate-500">Instruktur / Pemateri</dt>
                            <dd class="text-slate-800 sm:col-span-2">
                                {{ $registration->workshop->speaker->name }}
                                @if($registration->workshop->speaker->title_role)
                                    <span class="text-slate-500">&mdash; {{ $registration->workshop->speaker->title_role }}</span>
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                        <dt class="font-medium text-slate-500">Tanggal Pelaksanaan</dt>
                        <dd class="text-slate-800 sm:col-span-2">
                            {{ $registration->workshop?->start_date?->copy()->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 gap-1 py-3.5 sm:grid-cols-3 sm:gap-4">
                        <dt class="font-medium text-slate-500">Tanggal Terbit</dt>
                        <dd class="text-slate-800 sm:col-span-2">
                            {{ ($registration->attended_at ?? $registration->updated_at ?? now())->copy()->locale('id')->translatedFormat('d F Y') }}
                        </dd>
                    </div>
                </dl>
            </div>
        @else
            <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 p-6">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-600 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-rose-950">Sertifikat Tidak Ditemukan atau Tidak Aktif</h2>
                        <p class="mt-1 text-xs leading-relaxed text-rose-800">
                            Nomor sertifikat <span class="font-mono font-bold">{{ $code }}</span> tidak terdaftar atau fitur e-sertifikat untuk event terkait sedang tidak aktif. Pastikan penulisan nomor sudah benar.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endcomponent
