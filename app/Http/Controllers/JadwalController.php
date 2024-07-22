<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::with(["user","waktu","mapel","kelas"])->get();

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwal
        ], 200);
    }

    public function store(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'waktu_id' => 'required|numeric',
            'metode_pembelajaran' => 'required',
            'mapel_id' => 'required|numeric'
        ]);

        if($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $checkKelas = Kelas::where('id', $id)->exists();

        if(!$checkKelas) {
            return response()->json([
                'message' => 'ID kelas tidak ditemukan'
            ], 403);
        }

        $checkUser = User::where('id', $request->user_id)->exists();

        if(!$checkUser) {
            return response()->json([
                'message' => 'ID user tidak ditemukan'
            ], 403);
        }

        $data = [
            'user_id' => $request->user_id,
            'kelas_id' => $id,
            'waktu_id' => $request->waktu_id,
            'metode_pembelajaran' => $request->metode_pembelajaran,
            'mapel_id' => $request->mapel_id,
        ];

        $jadwal = Jadwal::create($data);

        return response()->json([
            'message' => 'jadwal berhasil dibuat'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        // LANJUT BESOK

        // $validated = Validator::make($request->all(), [
        //     'user_id' => 'required|numeric',
        //     'waktu_id' => 'required|numeric',
        //     'kelas_id' => 'required|numeric',
        //     'metode_pembelajaran' => 'required',
        //     'mapel_id' => 'required|numeric'
        // ]);

        // if($validated->fails()) {
        //     return response()->json($validated->errors(), 400);
        // }

        // $checkKelas = Kelas::where('id', $request->kelas)->exists();

        // if(!$checkKelas) {
        //     return response()->json([
        //         'message' => 'ID kelas tidak ditemukan'
        //     ], 403);
        // }

        // $checkUser = User::where('id', $request->user_id)->exists();

        // if(!$checkUser) {
        //     return response()->json([
        //         'message' => 'ID user tidak ditemukan'
        //     ], 403);
        // }

        // $jadwal = Jadwal::where('id', $id)->first();

        // if(!$jadwal) {
        //     return response()->json([
        //         'message' => 'jadwal tidak ditemukan'
        //     ]. 403);
        // }

    
    }
}
