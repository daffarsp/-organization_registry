<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'registration_number',
    'name',
    'email',
    'phone',
    'gender',
    'birth_date',
    'school',
    'address',
    'division_id',
    'reason',
    'organization_experience',
    'basic_question_answer',
    'instagram',
    'status',
    'score',
    'test_completed_at',
    'basic_completed_at',
    'notes',
])]
class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Registration $registration): void {
            $registration->status ??= RegistrationStatus::Pending;
            $registration->registration_number ??= self::generateRegistrationNumber();
        });
    }

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * @return HasMany<RegistrationAnswer, $this>
     */
    public function answers(): HasMany
    {
        return $this->hasMany(RegistrationAnswer::class);
    }

    /**
     * @return HasMany<RegistrationScreeningAnswer, $this>
     */
    public function screeningAnswers(): HasMany
    {
        return $this->hasMany(RegistrationScreeningAnswer::class);
    }

    public function isQuizCompleted(): bool
    {
        return $this->test_completed_at !== null || $this->score !== null;
    }

    public function hasCompletedBasicQuestion(): bool
    {
        return $this->basic_completed_at !== null;
    }

    public function canStartAdvancedQuiz(): bool
    {
        return $this->hasCompletedBasicQuestion() && ! $this->isQuizCompleted();
    }

    public function getRouteKeyName(): string
    {
        return 'registration_number';
    }

    public static function generateRegistrationNumber(?int $year = null): string
    {
        $year ??= now()->year;
        $prefix = "REG-{$year}-";

        $latestNumber = self::query()
            ->where('registration_number', 'like', "{$prefix}%")
            ->orderByDesc('registration_number')
            ->lockForUpdate()
            ->value('registration_number');

        $sequence = 1;

        if (is_string($latestNumber)) {
            $sequence = ((int) str($latestNumber)->afterLast('-')->toString()) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param Builder<Registration> $query
     */
    public function scopeCreatedToday(Builder $query): void
    {
        $query->whereBetween('created_at', [
            Carbon::today(),
            Carbon::tomorrow(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'score' => 'integer',
            'test_completed_at' => 'datetime',
            'basic_completed_at' => 'datetime',
            'status' => RegistrationStatus::class,
        ];
    }
}
