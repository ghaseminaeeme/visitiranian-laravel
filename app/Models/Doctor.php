<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Doctor extends Model
{
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'bio',
        'photo_path',
        'website',
        'address',
        'city_id',
        'primary_specialty_id',
        'sms_mobile',
        'name_normalized',
        'search_text',
        'meta_title',
        'meta_description',
        'is_published',
        'is_active',
        'is_vip',
        'published_at',
        'expires_at',
        'qr_code_path',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_active' => 'boolean',
            'is_vip' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function primarySpecialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'primary_specialty_id');
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty')
            ->withPivot('is_primary');
    }

    public function contactPhones(): HasMany
    {
        return $this->hasMany(DoctorContactPhone::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(DoctorSocialLink::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DoctorPhoto::class);
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class, 'doctor_clinic')
            ->withPivot('role');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function scheduleExceptions(): HasMany
    {
        return $this->hasMany(DoctorScheduleException::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(AppointmentWaitlist::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeVisible(Builder $query): Builder
    {
        $query->where('is_active', true)->where('is_published', true);

        $hideOnExpiry = (bool) app(\App\Services\Settings\SettingService::class)->get('hide_doctors_on_expiry', false);

        if ($hideOnExpiry) {
            $query->where(function (Builder $query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
        }

        return $query;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
