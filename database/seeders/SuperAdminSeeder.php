<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        SuperAdmin::updateOrCreate(
            ['super_email' => 'admin@zapiska.com'],
            [
                'super_username' => 'davicsalaciasquidrye',
                'super_password' => Hash::make('4>~G&@i_r8f8VU6V9*`#rqM'),
                'first_name' => 'Jinsol',
                'last_name' => 'Kim',
                'contact' => '+639123456789',
                'status' => 'active',
            ]
        );
    }
}
