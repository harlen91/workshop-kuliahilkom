<div class="space-y-4 text-sm">
    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-900">
        <p class="font-semibold text-base">Event: {{ $record->title }}</p>
        <p class="text-xs text-indigo-700 mt-1">
            Total Peserta Terdaftar: <strong>{{ $record->registrations()->count() }} orang</strong>
            @if($record->platform === 'zoom' && $record->meeting_url)
                &bull; Zoom URL: <a href="{{ $record->meeting_url }}" target="_blank" class="underline font-mono">{{ $record->meeting_url }}</a>
            @endif
        </p>
    </div>

    <!-- WhatsApp Group Info Card -->
    @if($record->hasWhatsappGroup())
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
            <div>
                <div class="flex items-center gap-2 font-bold text-xs uppercase tracking-wider text-emerald-800">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    Link WhatsApp Group Aktif
                </div>
                <div class="text-xs font-mono text-emerald-900 mt-0.5 break-all">
                    {{ $record->whatsapp_group_url }}
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ $record->whatsapp_group_url }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors">
                    Buka Grup
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    @else
        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span><strong>Perhatian:</strong> Link WhatsApp Group belum diisi untuk workshop ini. Klik tombol <strong>Link WA Grup</strong> di tabel untuk mengisi link undangan grup.</span>
        </div>
    @endif

    <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Daftar Kontak Peserta (Nama - No HP)</label>
        <textarea readonly rows="5" class="w-full text-xs font-mono bg-gray-50 border border-gray-300 rounded p-2 focus:ring-indigo-500 focus:border-indigo-500" onclick="this.select()">{{ $contacts ?: 'Belum ada peserta terdaftar.' }}</textarea>
    </div>

    @if($phoneList)
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Daftar Nomor WhatsApp (Format Koma untuk Aplikasi Blast)</label>
            <input type="text" readonly value="{{ $phoneList }}" class="w-full text-xs font-mono bg-gray-50 border border-gray-300 rounded p-2 focus:ring-indigo-500 focus:border-indigo-500" onclick="this.select()" />
            <p class="text-[11px] text-gray-500 mt-1">Klik pada kolom untuk memilih semua nomor secara otomatis.</p>
        </div>
    @endif

    <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Template 1: Broadcast Undangan WhatsApp Group</label>
        <textarea readonly rows="6" class="w-full text-xs bg-gray-50 border border-gray-300 rounded p-2 font-mono" onclick="this.select()">*UNDANGAN RESMI GRUP WHATSAPP PESERTA*
Halo Rekan Peserta!

Terima kasih telah mendaftar kegiatan:
*{{ $record->title }}*

Untuk kelancaran koordinasi, tanya jawab instruktur, serta pembagian materi dan link meeting, silakan bergabung ke Grup WhatsApp peserta:
👉 {{ $record->whatsapp_group_url ?: 'https://chat.whatsapp.com/...' }}

Sampai jumpa di grup!
— Tim KuliahIlkom</textarea>
        <p class="text-[11px] text-gray-500 mt-0.5">Template khusus untuk mengajak peserta bergabung ke grup WhatsApp.</p>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Template 2: Broadcast Reminder Lengkap Event</label>
        <textarea readonly rows="8" class="w-full text-xs bg-gray-50 border border-gray-300 rounded p-2 font-mono" onclick="this.select()">*REMINDER EVENT KULIAHILKOM*
Halo Rekan Peserta!

Mengingatkan kembali pelaksanaan kegiatan:
*{{ $record->title }}*
📅 Tanggal: {{ $record->start_date ? $record->start_date->isoFormat('dddd, D MMMM Y - HH:mm') : '-' }} {{ $record->timezone }}
📍 Platform: {{ strtoupper($record->platform) }}
🔗 Link Akses: {{ $record->meeting_url ?: 'Akan diinformasikan di grup' }}
@if($record->meeting_password)🔑 Passcode: {{ $record->meeting_password }}
@endif
@if($record->whatsapp_group_url)💬 Link WA Grup: {{ $record->whatsapp_group_url }}
@endif

Mohon hadir 10 menit sebelum acara dimulai. Sampai jumpa di ruang workshop!
— Tim KuliahIlkom</textarea>
        <p class="text-[11px] text-gray-500 mt-0.5">Template komprehensif reminder hari-H yang memuat link room & grup WhatsApp.</p>
    </div>
</div>
