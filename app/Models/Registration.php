<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Registration extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'workshop_id',
        'ticket_code',
        'attendance_status',
        'attended_at',
        'certificate_code',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Registration $registration): void {
            if (empty($registration->ticket_code)) {
                $registration->ticket_code = 'TKT-'.date('ym').'-'.Str::upper(Str::random(6));
            }
        });
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Workshop, $this>
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function isAttended(): bool
    {
        return $this->attendance_status === 'attended';
    }

    public function markAttended(): void
    {
        $this->update([
            'attendance_status' => 'attended',
            'attended_at' => now(),
            'certificate_code' => $this->certificate_code ?: 'CERT-'.date('Ymd').'-'.Str::upper(Str::random(6)),
        ]);
    }

    /**
     * Generate scannable QR Code SVG for ticket check-in.
     */
    public function getQrCodeSvgAttribute(): string
    {
        if (empty($this->ticket_code)) {
            return '';
        }

        try {
            $svg = (string) QrCode::size(140)
                ->margin(1)
                ->color(15, 23, 42)
                ->generate($this->ticket_code);

            return (string) preg_replace('/<\?xml.*?\?>/', '', $svg);
        } catch (\Throwable) {
            return '';
        }
    }
}
