<div class="space-y-4 text-sm">
    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-indigo-900">
        <p class="font-semibold">Event: {{ $record->title }}</p>
        <p class="text-xs text-indigo-700 mt-1">
            Total Peserta Terdaftar: <strong>{{ $record->registrations()->count() }} orang</strong>
            @if($record->platform === 'zoom' && $record->meeting_url)
                &bull; Zoom URL: <a href="{{ $record->meeting_url }}" target="_blank" class="underline font-mono">{{ $record->meeting_url }}</a>
            @endif
        </p>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Daftar Kontak Peserta (Nama - No HP)</label>
        <textarea readonly rows="6" class="w-full text-xs font-mono bg-gray-50 border border-gray-300 rounded p-2 focus:ring-indigo-500 focus:border-indigo-500" onclick="this.select()">{{ $contacts ?: 'Belum ada peserta terdaftar.' }}</textarea>
    </div>

    @if($phoneList)
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Daftar Nomor WhatsApp (Format Koma untuk Aplikasi Blast)</label>
            <input type="text" readonly value="{{ $phoneList }}" class="w-full text-xs font-mono bg-gray-50 border border-gray-300 rounded p-2 focus:ring-indigo-500 focus:border-indigo-500" onclick="this.select()" />
            <p class="text-[11px] text-gray-500 mt-1">Klik pada kolom untuk memilih semua nomor secara otomatis.</p>
        </div>
    @endif

    <div>
        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Template Broadcast Rekomendasi</label>
        <textarea readonly rows="5" class="w-full text-xs bg-gray-50 border border-gray-300 rounded p-2" onclick="this.select()">*REMINDER EVENT KULIAHILKOM*
Halo Rekan Peserta!

Mengingatkan kembali pelaksanaan kegiatan:
*{{ $record->title }}*
📅 Tanggal: {{ $record->start_date ? $record->start_date->isoFormat('dddd, D MMMM Y - HH:mm') : '-' }} {{ $record->timezone }}
📍 Platform: {{ strtoupper($record->platform) }}
🔗 Link Akses: {{ $record->meeting_url ?: 'Akan diinformasikan di grup' }}
@if($record->meeting_password)🔑 Passcode: {{ $record->meeting_password }}@endif

Mohon hadir 10 menit sebelum acara dimulai. Sampai jumpa di ruang workshop!
— Tim KuliahIlkom</textarea>
    </div>
</div>
