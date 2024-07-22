<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenController extends Controller
{
    function index()
    {
        $user = Auth::user();
        $absen = Absen::with(["user","waktu","mapel","kelas"])->where("user_id", $user->id)->get();

        return response()->json([
            "message" => "Data berhasil didapatkan",
            "data" => $absen
        ], 200);
    }

    function store(Kelas $kelas, Request $request) {
        $user = Auth::user();

        $jadwal = Jadwal::where('user_id', $user->id)
        ->where('kelas_id', $kelas->id)
        ->where('metode_pembelajaran', $request->metode_pembelajaran)
        ->where('mapel_id', $request->mapel_id)
        ->where('waktu_id', $request->waktu_id)
        ->exists();

        if(!$jadwal) {
            return response()->json([
                'message' => 'Maaf, anda tidak terdaftar di jadwal ini'
            ], 401);
        }

        $absen = Absen::where('user_id', $user->id)
        ->where('kelas_id', $kelas->id)
        ->where('mapel_id', $request->mapel_id)
        ->where('waktu_id', $request->waktu_id)
        ->where('tanggal', date('Y-m-d'))
        ->first();

        if($absen) {
            return response()->json([
                'message' => 'Maaf, anda sudah absen dikelas ini'
            ], 401);
        }

        $data = [
            "user_id" => $user->id,
            "kelas_id" => $kelas->id,
            "metode_pembelajaran" => $request->metode_pembelajaran,
            "mapel_id" => $request->mapel_id,
            "waktu_id" => $request->waktu_id,
            'tanggal' => date('Y-m-d')
        ];
        
        $absen = Absen::create($data);

        if ($absen) {
            return response()->json([
                "message" => "absen berhasil",
            ], 201);
        } else {
            return response()->json([
                "message" => "absen gagal silahkan hubungi admin"
            ], 401);
        }
    }
}
