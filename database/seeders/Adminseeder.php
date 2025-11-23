<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user (owner/super admin)
        User::create([
            'name' => 'Admin TastePoint',
            'email' => 'admin@tastepoint.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Optional: Create a demo regular user
        // User::create([
        //     'name' => 'User Demo',
        //     'email' => 'user@tastepoint.com',
        //     'password' => Hash::make('user123456'),
        //     'role' => 'user',
        //     'email_verified_at' => now(),
        // ]);

        echo "✅ Admin user created successfully!\n";
        echo "📧 Email: admin@tastepoint.com\n";
        echo "🔑 Password: admin123\n";
        echo "\n⚠️  PENTING: Ganti password admin setelah login pertama!\n";
    }
}