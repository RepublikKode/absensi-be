<?php

namespace Database\Seeders;

use App\Models\Waktu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WaktuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 1; $i <= 11; $i++) {
           Waktu::create([
                "jamke" => "Jam ke $i"
            ]);
        }
    }
}
