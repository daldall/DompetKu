<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('ADMIN_EMAIL', 'admin@dompetku.test');
        $password = (string) env('ADMIN_PASSWORD', 'admin12345');
        $nama = (string) env('ADMIN_NAMA', 'Admin DompetKu');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'nama' => $nama,
                'password' => Hash::make($password),
                'role' => User::ROLE_ADMIN,
                'bio' => 'Akun admin untuk demo/pengelolaan aplikasi.',
            ]
        );
    }
}
