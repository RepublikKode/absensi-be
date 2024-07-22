<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jadwal::create([
            'user_id' => 2,
            'kelas_id' => 1,
            'waktu_id' => 1,
            'metode_pembelajaran' => 'plk',
            'mapel_id' => 7,
        ]);
        
        Jadwal::create([
            'user_id' => 2,
            'kelas_id' => 1,
            'waktu_id' => 1,
            'metode_pembelajaran' => 'plk',
            'mapel_id' => 7,
        ]);

        Jadwal::create([
            'user_id' => 2,
            'kelas_id' => 1,
            'waktu_id' => 1,
            'metode_pembelajaran' => 'plk',
            'mapel_id' => 7,
        ]);
    }
}
