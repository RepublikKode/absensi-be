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

        // Mengelompokkan data berdasarkan minggu, kemudian hari, dan akhirnya kelas
        $groupedByMinggu = $jadwal->groupBy('minggu')->map(function ($items) {
            // Mengelompokkan lebih lanjut berdasarkan hari
            return $items->groupBy('hari')->map(function ($dayItems) {
                // Mengelompokkan lebih lanjut berdasarkan kelas
                return $dayItems->groupBy('kelas.fix_kelas');
            });
        });

        return response()->json([
            'message' => 'Jadwal berhasil diambil',
            'data' => $groupedByMinggu
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
            ->orderBy('minggu')
            ->orderBy('hari')
            ->get()
            ->groupBy(function ($item) {
                return $item->hari;
            })
            ->map(function ($group) {
                return $group->groupBy('minggu');
            });

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
            'ruang' => 'required',
            'minggu' => 'required',
            'hari' => 'required',
            'metode_pembelajaran' => 'required',
            'mapel_id' => 'required|numeric',
            'jurusan_id' => 'required',
            'total_jam' => 'required|numeric|min:1' // total_jam harus angka minimal 1
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

        // Ambil waktu terakhir untuk menentukan waktuID
        $getWaktu = Jadwal::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where('minggu', $request->minggu)
            ->orderBy('waktu_id', 'desc')
            ->first();

        if ($getWaktu) {
            $waktuID = $getWaktu->waktu_id + 1;
        } else {
            $waktuID = 1;
        }

        // Loop untuk membuat jadwal sesuai total_jam
        // Loop untuk membuat jadwal sesuai total_jam
        for ($i = 0; $i < $request->total_jam; $i++) {
            $currentWaktuID = $waktuID + $i; // waktu_id yang akan dicek

            // Cek apakah guru sudah terdaftar pada waktu yang sama di hari yang sama
            $checkUser = Jadwal::where('user_id', $request->user_id)
                ->where('hari', $request->hari)
                ->where('minggu', $request->minggu)
                ->where('waktu_id', $currentWaktuID) // cek berdasarkan waktu_id yang akan digunakan
                ->first();

            if ($checkUser) {
                // Jika ruang yang sudah ada sama dengan ruang dalam request, lanjutkan
                if ($checkUser->ruang === $request->ruang) {
                    // Berarti ruang yang sama, tidak ada konflik, lanjutkan ke pembuatan jadwal
                    continue;
                } else {
                    // Jika ruang berbeda, kembalikan respons error
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Guru sudah terdaftar di kelas lain pada jam ' . $currentWaktuID . ' di ruang berbeda'
                    ], 400);
                }
            }

            // Data jadwal yang akan dibuat
            $data = [
                'user_id' => $request->user_id,
                'kelas_id' => $request->kelas_id,
                'ruang' => $request->ruang,
                'minggu' => $request->minggu,
                'waktu_id' => $currentWaktuID, // waktu_id bertambah setiap iterasi
                'hari' => $request->hari,
                'metode_pembelajaran' => $request->metode_pembelajaran,
                'mapel_id' => $request->mapel_id,
                'jurusan_id' => $request->jurusan_id,
            ];

            // Simpan data jadwal
            Jadwal::create($data);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil dibuat'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'waktu_id' => 'required|numeric',
            'ruang' => 'required',
            'minggu' => 'required',
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
        $jadwal->ruang = $request->ruang;
        $jadwal->minggu = $request->minggu;
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
