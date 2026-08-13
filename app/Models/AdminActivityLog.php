<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

#[Fillable([
    'user_id',
    'actor_name',
    'actor_email',
    'action',
    'subject_type',
    'subject_id',
    'subject_label',
    'description',
    'metadata',
])]
class AdminActivityLog extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public static function record(
        string $action,
        ?Model $subject,
        string $description,
        array $metadata = [],
    ): ?self {
        $user = Auth::user();

        if (! $user instanceof User || ! $user->isAdmin()) {
            return null;
        }

        return self::query()->create([
            'user_id' => $user->id,
            'actor_name' => $user->name,
            'actor_email' => $user->email,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'subject_label' => $subject ? self::labelFor($subject) : null,
            'description' => $description,
            'metadata' => $metadata ?: null,
        ]);
    }

    /**
     * @param array<string, mixed> $changes
     */
    public static function recordModelChange(string $action, Model $subject, array $changes = []): ?self
    {
        return self::record(
            $action,
            $subject,
            self::descriptionFor($action, $subject),
            ['changes' => $changes],
        );
    }

    public static function labelFor(Model $subject): string
    {
        if ($subject instanceof Registration) {
            return trim("{$subject->registration_number} - {$subject->name}");
        }

        if ($subject instanceof Division) {
            return $subject->name;
        }

        if ($subject instanceof Question) {
            return str($subject->question_text)->limit(80)->toString();
        }

        if ($subject instanceof ScreeningQuestion) {
            return str($subject->question_text)->limit(80)->toString();
        }

        if ($subject instanceof User) {
            return trim("{$subject->name} ({$subject->email})");
        }

        return class_basename($subject).' #'.$subject->getKey();
    }

    private static function descriptionFor(string $action, Model $subject): string
    {
        $label = self::labelFor($subject);

        return match ($action) {
            'created' => "Membuat {$label}",
            'updated' => "Memperbarui {$label}",
            'deleted' => "Menghapus {$label}",
            default => ucfirst($action).' '.$label,
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
