<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = env('ADMIN_USERNAME');
        $password = env('ADMIN_PASSWORD');

        if (!$username || !$password) {
            throw new \RuntimeException(
                'ADMIN_USERNAME dan ADMIN_PASSWORD harus diisi di file .env'
            );
        }

        User::updateOrCreate(
            ['username' => $username],
            [
                'name' => 'Admin Izam Jaya',
                'password' => $password,
            ]
        );
    }
}