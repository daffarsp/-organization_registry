<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['password'] ?? null)) {
            throw ValidationException::withMessages([
                'data.password' => 'Password wajib diisi saat membuat akun panel.',
            ]);
        }

        $data['is_admin'] = (bool) ($data['is_admin'] ?? true);

        return $data;
    }
}
