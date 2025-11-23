<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Menu;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin TastePoint',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);

        $r1 = Restaurant::create([
            'name' => 'Warung Nusantara',
            'type' => 'restaurant',
            'status' => 'open',
            'distance' => '1.2 km',
            'price_range' => 'Rp 25.000 - 75.000',
            'photo' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400',
            'is_24_hours' => false,
            'avg_price' => 50000,
            'location' => 'Jl. Pendidikan No. 123, Jakarta Pusat',
            'description' => 'Restoran masakan Indonesia tradisional',
            'rating' => 4.5,
            'views' => 156,
        ]);

        Menu::create(['restaurant_id' => $r1->id, 'name' => 'Nasi Goreng Spesial', 'price' => 25000, 'category' => 'makanan', 'is_popular' => true]);
        Menu::create(['restaurant_id' => $r1->id, 'name' => 'Ayam Bakar Madu', 'price' => 35000, 'category' => 'makanan', 'is_popular' => true]);
        Menu::create(['restaurant_id' => $r1->id, 'name' => 'Es Jeruk', 'price' => 12000, 'category' => 'minuman']);
    }
}
