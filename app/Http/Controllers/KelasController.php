<?php

namespace App\Http\Controllers;

use App\Http\Requests\KelasRequest;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    function index() {
        $kelas = Kelas::with(['jurusan'])
                      ->get();

        if ($kelas) {
            return response()->json([
                "message" => "sukses",
                "kelas" => $kelas
            ], 200);
        } else {
            return response()->json([
                "message" => "data kelas kosong"
            ], 404);
        }
    }

    function store(KelasRequest $request) {
        $data = $request->validated();

        $kelas = Kelas::create($data);

        if ($kelas) {
            return response()->json([
                "message" => "kelas berhasil ditambahkan!"
            ], 201);
        } else {
            return response()->json([
                "message" => "kelas gagal ditambahkan"
            ], 401);
        }
    }

    function show($id) {
        $kelas = Kelas::where('kelas.id', $id)
                      ->join('jurusans', 'jurusan_id', '=', 'jurusans.id')
                      ->first()
                      ->load("absen");

        if ($kelas) {
            return response()->json([
                "message" => "data ditemukan",
                "kelas" => $kelas
            ], 200);
        } else {
            return response()->json([
                "message" => "data tidak ditemukan"
            ], 404);
        }
    }

    function edit(Request $request, $id) {
        $kelas = Kelas::firstWhere("id", $id);
        $kelas->update([
            "kelas" => $request->kelas,
            "jurusan_id" => $request->jurusan_id,
            "alphabet" => $request->alphabet
        ]);
        $kelas->save();

        return response()->json([
            "message" => "data berhasil diubah"
        ], 200);
    }

    function destroy($id) {
        $kelas = Kelas::firstWhere("id", $id);        
        $kelas->delete();

        return response()->json([
            "message" => "data berhasil dihapus"
        ], 200);
    }
}
