<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['farel', 'Farrel Liesdia Putra', 'farel@gmail.com', [
                ['Jaket Parasut', 'Jaket parasut hitam ukuran L, masih layak pakai.', 'Good condition', 50000, 100000],
                ['Totebag Kanvas', 'Totebag kanvas polos, jarang dipakai.', 'Like new', 20000, 40000],
            ]],
            ['adrian', 'Adrian Fathinhady', 'adrian@gmail.com', [
                ['Kemeja Flanel', 'Kemeja flanel kotak-kotak ukuran M.', 'Good condition', 40000, 80000],
                ['Novel Bekas', 'Novel fiksi, kondisi halaman utuh.', 'Fair', 15000, 30000],
            ]],
            ['kian', 'Kian Islam Susanto', 'kian@gmail.com', [
                ['Tas Ransel', 'Tas ransel muat laptop 14 inci.', 'Good condition', 80000, 150000],
                ['Headset Kabel', 'Headset kabel 3.5mm, suara jernih.', 'Like new', 30000, 60000],
            ]],
        ];

        foreach ($data as [$username, $name, $email, $items]) {
            $user = User::create([
                'username' => $username, 'name' => $name, 'email' => $email, 'password' => 'password123',
            ]);
            foreach ($items as [$nama, $desk, $kondisi, $min, $max]) {
                Item::create([
                    'user_id' => $user->id, 'nama_barang' => $nama, 'deskripsi' => $desk,
                    'kondisi' => $kondisi, 'harga_min' => $min, 'harga_max' => $max,
                ]);
            }
        }
    }
}
