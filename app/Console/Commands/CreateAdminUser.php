<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--email=} {--name=} {--password=} {--role=admin : admin atau super_admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Buat atau update akun admin untuk login ke Filament Admin Panel';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email') ?: text(
            label: 'Masukkan Email Admin',
            default: 'admin@example.com',
            required: true
        );

        $name = $this->option('name') ?: text(
            label: 'Masukkan Nama Admin',
            default: 'Admin Organization',
            required: true
        );

        $plainPassword = $this->option('password') ?: password(
            label: 'Masukkan Password Admin',
            default: 'password',
            required: true
        );

        $role = (string) $this->option('role');

        if (! in_array($role, ['admin', 'super_admin'], true)) {
            throw new InvalidArgumentException('Role harus admin atau super_admin.');
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($plainPassword),
                'is_admin' => true,
                'role' => $role,
            ]
        );

        $this->info("Akun Admin berhasil dibuat/diupdate!");
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Password', '********'],
                ['Is Admin', $user->is_admin ? 'Yes' : 'No'],
                ['Role', $user->role],
                ['Login URL', url('/admin')],
            ]
        );

        return self::SUCCESS;
    }
}
