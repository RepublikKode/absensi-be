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
        Schema::create('absens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->unsignedBigInteger("kelas_id");
            $table->unsignedBigInteger("mapel_id");
            $table->unsignedBigInteger("waktu_id");
            $table->enum("metode_pembelajaran", ["pjj", "plk"]);
            $table->enum('hari', [
                'senin',
                'selasa',
                'rabu',
                'kamis',
                'jumat',
                'sabtu',
                'minggu'
            ]);
            $table->integer('jurusan_id');
            $table->string('tanggal');
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("kelas_id")->references("id")->on("kelas");
            $table->foreign("mapel_id")->references("id")->on("mapels");
            $table->foreign("waktu_id")->references('id')->on("waktus");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
