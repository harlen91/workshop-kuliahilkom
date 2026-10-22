<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Workshop extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'speaker_id',
        'title',
        'slug',
        'short_description',
        'description',
        'banner_path',
        'type',
        'start_date',
        'end_date',
        'timezone',
        'platform',
        'meeting_url',
        'meeting_password',
        'is_paid',
        'price',
        'original_price',
        'quota',
        'remaining_quota',
        'status',
        'certificate_enabled',
        'recording_url',
        'materials_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_paid' => 'boolean',
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'quota' => 'integer',
            'remaining_quota' => 'integer',
            'certificate_enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Workshop $workshop): void {
            if (empty($workshop->slug)) {
                $workshop->slug = Str::slug($workshop->title).'-'.Str::lower(Str::random(5));
            }
            if ($workshop->quota !== null && $workshop->remaining_quota === null) {
                $workshop->remaining_quota = $workshop->quota;
            }
        });
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Speaker, $this>
     */
    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<Registration, $this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Scope query to published workshops.
     *
     * @param  Builder<Workshop>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    /**
     * Scope query to upcoming workshops.
     *
     * @param  Builder<Workshop>  $query
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('end_date', '>=', now());
    }

    /**
     * Check if quota is available.
     */
    public function hasQuota(): bool
    {
        if ($this->quota === null) {
            return true;
        }

        return ($this->remaining_quota ?? 0) > 0;
    }

    /**
     * Check if workshop is sold out.
     */
    public function isSoldOut(): bool
    {
        return ! $this->hasQuota();
    }

    /**
     * Check if workshop is free.
     */
    public function isFree(): bool
    {
        return ! $this->is_paid || (float) $this->price <= 0;
    }

    /**
     * Format price to Indonesian Rupiah.
     */
    public function formattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Gratis';
        }

        return 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }

    /**
     * Format original price to Indonesian Rupiah.
     */
    public function formattedOriginalPrice(): ?string
    {
        if (! $this->original_price || (float) $this->original_price <= (float) $this->price) {
            return null;
        }

        return 'Rp '.number_format((float) $this->original_price, 0, ',', '.');
    }

    /**
     * Get banner URL from public storage disk or fallback URL.
     */
    public function getBannerUrlAttribute(): string
    {
        if (empty($this->banner_path)) {
            return 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=1200&q=80';
        }

        if (str_starts_with($this->banner_path, 'http://') || str_starts_with($this->banner_path, 'https://')) {
            return $this->banner_path;
        }

        return Storage::disk('public')->url($this->banner_path);
    }

    /**
     * Ensure meeting_url always has valid http/https protocol scheme.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function meetingUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            },
            set: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            }
        );
    }

    /**
     * Ensure recording_url always has valid http/https protocol scheme.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function recordingUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            },
            set: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            }
        );
    }

    /**
     * Ensure materials_url always has valid http/https protocol scheme.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function materialsUrl(): Attribute
    {
        return Attribute::make(
            get: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            },
            set: function (?string $value): ?string {
                if (empty($value) || trim($value) === '#' || trim($value) === '') {
                    return null;
                }

                $url = trim($value);

                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                    return 'https://'.ltrim($url, '/');
                }

                return $url;
            }
        );
    }
}
