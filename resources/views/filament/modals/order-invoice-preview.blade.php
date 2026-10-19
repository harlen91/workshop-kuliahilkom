<div class="space-y-4 text-sm">
    <div class="flex justify-between items-start border-b border-gray-200 pb-3">
        <div>
            <h3 class="text-base font-bold text-gray-900">FAKTUR PEMBAYARAN</h3>
            <p class="font-mono text-xs text-gray-500 mt-0.5">No. Invoice: <span class="font-bold text-indigo-600">{{ $record->order_code }}</span></p>
        </div>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
            {{ $record->status === 'paid' ? 'bg-green-100 text-green-800' : ($record->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
            {{ strtoupper($record->status) }}
        </span>
    </div>

    <div class="grid grid-cols-2 gap-3 text-xs bg-gray-50 p-3 rounded-lg border border-gray-200">
        <div>
            <span class="text-gray-500 block">Diterbitkan Untuk:</span>
            <span class="font-bold text-gray-900">{{ $record->user?->name }}</span>
            <span class="text-gray-600 block">{{ $record->user?->email }}</span>
            <span class="text-gray-600 block">{{ $record->user?->phone_number ?: '-' }}</span>
        </div>
        <div>
            <span class="text-gray-500 block">Metode Pembayaran:</span>
            <span class="font-semibold text-gray-900 uppercase">{{ $record->payment_type ?: 'ONLINE (MIDTRANS)' }}</span>
            <span class="text-gray-500 block mt-1">Waktu Transaksi:</span>
            <span class="text-gray-800">{{ $record->created_at ? $record->created_at->format('d M Y, H:i') : '-' }} WIB</span>
            @if($record->paid_at)
                <span class="text-gray-500 block mt-1">Waktu Lunas:</span>
                <span class="text-green-700 font-semibold">{{ $record->paid_at->format('d M Y, H:i') }} WIB</span>
            @endif
        </div>
    </div>

    <div>
        <table class="w-full text-xs border border-gray-200 rounded">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-2 text-left">Deskripsi Event / Workshop</th>
                    <th class="p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="p-2">
                        <div class="font-semibold text-gray-900">{{ $record->workshop?->title }}</div>
                        <div class="text-[11px] text-gray-500">Jadwal: {{ $record->workshop?->start_date ? $record->workshop->start_date->format('d M Y, H:i') : '-' }} {{ $record->workshop?->timezone }}</div>
                    </td>
                    <td class="p-2 text-right font-mono font-bold text-gray-900">
                        Rp {{ number_format((float) $record->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200 font-bold">
                <tr>
                    <td class="p-2 text-right">Total Tagihan:</td>
                    <td class="p-2 text-right text-indigo-700 font-mono text-sm">
                        Rp {{ number_format((float) $record->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($record->midtrans_transaction_id)
        <div class="text-[11px] text-gray-500 bg-gray-50 p-2 rounded border border-gray-200 font-mono">
            Midtrans Transaction ID: {{ $record->midtrans_transaction_id }}
        </div>
    @endif
</div>
