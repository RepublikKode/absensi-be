<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kelas::create([
            "kelas" => "X",
            "jurusan_id" => 1,
            "alphabet" => 'A',
            "fix_kelas" => "X PPLG A"
        ]);

        Kelas::create([
            "kelas" => "X",
            "jurusan_id" => 1,
            "alphabet" => 'B',
            "fix_kelas" => "X PPLG B"
        ]);
    }
}
