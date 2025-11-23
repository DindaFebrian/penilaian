<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $admin = User::firstOrCreate(
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
                'username' => 'admin',
            ]
        );
        $admin->assignRole('admin');

        $direktur = User::firstOrCreate(
            [
                'name' => 'TK Pertiwi',
                'password'=> bcrypt('password123'),
                'username' => 'NPSN',
            ]
        );
        $direktur->assignRole('sekolah');

        $pengawas = User::firstOrCreate(
            [
                'name' => 'Ai Anisah',
                'password' => bcrypt('password123'),
                'username' => 'NIP',
            ]
        );
        $pengawas->assignRole('pengawas');
    }
}
