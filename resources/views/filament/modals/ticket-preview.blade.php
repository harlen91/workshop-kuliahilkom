<div class="space-y-4 text-center">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-4 rounded-xl shadow">
        <p class="text-xs uppercase tracking-wider text-indigo-200 font-semibold">Official Event Ticket</p>
        <h3 class="text-lg font-bold mt-1">{{ $record->workshop?->title }}</h3>
        <p class="text-xs text-indigo-100 mt-1">
            {{ $record->workshop?->start_date ? $record->workshop->start_date->isoFormat('dddd, D MMMM Y • HH:mm') : '-' }} {{ $record->workshop?->timezone }}
        </p>
    </div>

    <div class="flex justify-center p-4 bg-white rounded-xl border border-gray-200 inline-block mx-auto shadow-sm">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate($record->ticket_code) !!}
    </div>

    <div class="font-mono text-lg font-extrabold text-gray-800 tracking-wider">
        {{ $record->ticket_code }}
    </div>

    <div class="text-left bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs space-y-1.5">
        <div class="flex justify-between">
            <span class="text-gray-500">Nama Peserta:</span>
            <span class="font-semibold text-gray-900">{{ $record->user?->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Email:</span>
            <span class="font-medium text-gray-800">{{ $record->user?->email }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">WhatsApp:</span>
            <span class="font-medium text-gray-800">{{ $record->user?->phone_number ?: '-' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Status Kehadiran:</span>
            <span class="font-bold {{ $record->isAttended() ? 'text-green-600' : 'text-amber-600' }}">
                {{ $record->isAttended() ? 'HADIR (OK)' : 'BELUM HADIR' }}
            </span>
        </div>
        @if($record->certificate_code)
            <div class="flex justify-between border-t border-gray-200 pt-1.5">
                <span class="text-gray-500">No. Sertifikat:</span>
                <span class="font-mono font-bold text-indigo-600">{{ $record->certificate_code }}</span>
            </div>
        @endif
    </div>
</div>
