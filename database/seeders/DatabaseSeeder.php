<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');

        if (App::isProduction() && blank($adminPassword)) {
            throw new RuntimeException('ADMIN_PASSWORD wajib di-set saat menjalankan seeder di production.');
        }

        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'superadmin@example.com'),
        ], [
            'name' => env('ADMIN_NAME', 'Admin Organization'),
            'password' => $adminPassword ?: 'password',
            'is_admin' => true,
            'role' => env('ADMIN_ROLE', 'super_admin'),
        ]);

        User::query()->updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin Biasa',
            'password' => $adminPassword ?: 'password',
            'is_admin' => true,
            'role' => 'admin',
        ]);

        collect([
            ['name' => 'IT', 'description' => 'Mengelola kebutuhan teknologi, website, data, dan sistem internal organisasi.'],
            ['name' => 'Humas', 'description' => 'Menjaga komunikasi eksternal, relasi publik, dan kerja sama komunitas.'],
            ['name' => 'Acara', 'description' => 'Merancang, mengeksekusi, dan mengevaluasi kegiatan organisasi.'],
            ['name' => 'Publikasi', 'description' => 'Mengelola konten, desain publikasi, dokumentasi, dan media sosial.'],
            ['name' => 'Keuangan', 'description' => 'Mengelola anggaran, laporan keuangan, dan kebutuhan pendanaan kegiatan.'],
            ['name' => 'SDM', 'description' => 'Mengembangkan anggota, kaderisasi, internal bonding, dan budaya organisasi.'],
        ])->each(fn (array $division): Division => Division::query()->updateOrCreate(
            ['name' => $division['name']],
            $division + ['is_active' => true],
        ));

        $this->call(ScreeningQuestionSeeder::class);
    }
}
