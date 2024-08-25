<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->enum("kelas", [
                'X',
                'XI',
                'XII',
                'XIII'
            ]);
            $table->integer('jurusan_id');
            $table->enum('alphabet', range('A', 'Z')); // mengambil A-Z 
            $table->integer("list")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
