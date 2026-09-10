<?php

namespace Database\Seeders;

use App\Models\LabItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Lab',
            'email' => 'admin@labkom.test',
            'phone' => '081234567890',
            'password' => 'password',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Mahasiswa Contoh',
            'email' => 'mahasiswa@labkom.test',
            'phone' => '081298765432',
            'password' => 'password',
            'role' => 'member',
        ]);

        $items = [
            ['code' => 'KOM-HW-001', 'name' => 'Laptop Acer TravelMate', 'category' => 'Hardware', 'unit' => 'unit', 'stock' => 15, 'description' => 'Laptop untuk praktikum pemrograman.'],
            ['code' => 'KOM-HW-002', 'name' => 'PC Workstation', 'category' => 'Hardware', 'unit' => 'unit', 'stock' => 10, 'description' => 'PC dengan spesifikasi tinggi untuk desain grafis.'],
            ['code' => 'KOM-HW-003', 'name' => 'Proyektor LCD', 'category' => 'Hardware', 'unit' => 'unit', 'stock' => 4, 'description' => 'Proyektor untuk presentasi kelas.'],
            ['code' => 'KOM-HW-004', 'name' => 'Router Mikrotik', 'category' => 'Jaringan', 'unit' => 'unit', 'stock' => 8, 'description' => 'Router untuk praktikum jaringan komputer.'],
            ['code' => 'KOM-HW-005', 'name' => 'Kabel UTP', 'category' => 'Jaringan', 'unit' => 'roll', 'stock' => 12, 'description' => 'Kabel UTP Cat6 untuk praktikum jaringan.'],
            ['code' => 'KOM-HW-006', 'name' => 'Kamera DSLR', 'category' => 'Multimedia', 'unit' => 'unit', 'stock' => 3, 'description' => 'Kamera untuk praktikum multimedia dan fotografi.'],
            ['code' => 'KOM-HW-007', 'name' => 'Arduino Uno', 'category' => 'IoT', 'unit' => 'unit', 'stock' => 20, 'description' => 'Mikrokontroler untuk praktikum IoT.'],
            ['code' => 'KOM-HW-008', 'name' => 'Sensor RFID', 'category' => 'IoT', 'unit' => 'buah', 'stock' => 25, 'description' => 'Modul sensor RFID Untuk praktikum IoT.'],
        ];

        foreach ($items as $item) {
            LabItem::create($item);
        }
    }
}
