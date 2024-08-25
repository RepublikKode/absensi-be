<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;
    protected $table = "kelas";
    protected $guarded = ["id"];

    function absen() : HasMany {
        return $this->hasMany(Absen::class, "kelas_id");
    }

    function jurusan(): BelongsTo {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }
}
