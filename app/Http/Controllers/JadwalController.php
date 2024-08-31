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
        $jadwal = Jadwal::with(["user", "waktu", "mapel", "kelas"])->get();

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwal
        ], 200);
    }

    public function getAllJadwal() 
    {
        // Mengambil data jadwal beserta relasinya
        $jadwal = Jadwal::with(["user", "waktu", "mapel", "kelas", "jurusan"])->get();

        // Mengelompokkan data berdasarkan kelas
        $groupedByKelas = $jadwal->groupBy('kelas.fix_kelas')->map(function ($items) {
            // Mengelompokkan lebih lanjut berdasarkan hari
            return $items->groupBy('hari');
        });

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $groupedByKelas
        ], 200);
    }

    public function show($id)
    {
        $jadwal = Jadwal::with(["user", "waktu", "mapel", "kelas", "jurusan"])
            ->where('id', $id)
            ->first();

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwal
        ], 200);
    }

    public function jadwalKelas($id)
    {
        $jadwal = Jadwal::with(["user", "waktu", "mapel", "kelas", "jurusan"])
            ->where('kelas_id', $id)
            ->get();

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwal
        ], 200);
    }

    public function jadwalDetail($id)
    {
        $jadwal = Jadwal::with(["user", "waktu", "mapel", "kelas", "jurusan"])
            ->where('kelas_id', $id)
            ->orderBy('hari')
            ->get()
            ->groupBy('hari');

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $jadwal
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'kelas_id' => 'required|numeric',
            'hari' => 'required',
            'metode_pembelajaran' => 'required',
            'mapel_id' => 'required|numeric',
            'jurusan_id' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $checkJadwal = Jadwal::where('kelas_id', $request->kelas_id)
            ->where('waktu_id', $request->waktu_id)
            ->where('hari', $request->hari)
            ->exists();

        if ($checkJadwal) {
            return response()->json([
                'message' => 'Jadwal ini sudah terdaftar'
            ], 409);
        }

        $checkKelas = Kelas::where('id', $request->kelas_id)->exists();

        if (!$checkKelas) {
            return response()->json([
                'message' => 'ID kelas tidak ditemukan'
            ], 403);
        }

        $checkUser = User::where('id', $request->user_id)->exists();

        if (!$checkUser) {
            return response()->json([
                'message' => 'ID user tidak ditemukan'
            ], 403);
        }

        
        $getWaktu = Jadwal::where('kelas_id', $request->kelas_id)
                          ->where('hari', $request->hari)
                          ->latest()
                          ->first();

        if($getWaktu) {
            $waktuID = $getWaktu->waktu_id + 1;
        } else {
            $waktuID = 1;
        }

        $data = [
            'user_id' => $request->user_id,
            'kelas_id' => $request->kelas_id,
            'waktu_id' => $waktuID,
            'hari' => $request->hari,
            'metode_pembelajaran' => $request->metode_pembelajaran,
            'mapel_id' => $request->mapel_id,
            'jurusan_id' => $request->jurusan_id,
        ];

        $jadwal = Jadwal::create($data);

        return response()->json([
            'message' => 'jadwal berhasil dibuat'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'waktu_id' => 'required|numeric',
            'hari' => 'required',
            'kelas_id' => 'required|numeric',
            'metode_pembelajaran' => 'required',
            'mapel_id' => 'required|numeric',
            'jurusan_id' => 'required'
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $checkJadwal = Jadwal::where('user_id', $request->user_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('waktu_id', $request->waktu_id)
            ->where('hari', $request->hari)
            ->where('metode_pembelajaran', $request->metode_pembelajaran)
            ->where('mapel_id', $request->mapel_id)
            ->exists();

        if ($checkJadwal) {
            return response()->json([
                'message' => 'Jadwal ini sudah terdaftar'
            ], 409);
        }

        $checkKelas = Kelas::where('id', $request->kelas_id)->exists();

        if (!$checkKelas) {
            return response()->json([
                'message' => 'ID kelas tidak ditemukan'
            ], 403);
        }

        $checkUser = User::where('id', $request->user_id)->exists();

        if (!$checkUser) {
            return response()->json([
                'message' => 'ID user tidak ditemukan'
            ], 403);
        }

        $jadwal = Jadwal::where('id', $id)->first();

        if (!$jadwal) {
            return response()->json([
                'message' => 'jadwal tidak ditemukan'
            ] . 403);
        }

        $jadwal->user_id = $request->user_id;
        $jadwal->waktu_id = $request->waktu_id;
        $jadwal->hari = $request->hari;
        $jadwal->kelas_id = $request->kelas_id;
        $jadwal->metode_pembelajaran = $request->metode_pembelajaran;
        $jadwal->mapel_id = $request->mapel_id;
        $jadwal->jurusan_id = $request->jurusan_id;
        $jadwal->save();

        return response()->json([
            'message' => 'Jadwal berhasil di update'
        ], 201);
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::where('id', $id)->first();

        if (!$jadwal) {
            return response()->json([
                'message' => 'jadwal tidak ditemukan'
            ], 403);
        }

        $jadwal->delete();

        return response()->json([
            'message' => 'Jadwal berhasil dihapus'
        ], 201);
    }
}
