<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JurusanController extends Controller
{
    function index() {
        $jurusan = Jurusan::all();

        return $jurusan;
    }

    public function show($id)
    {
        $jurusan = Jurusan::where('id', $id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $jurusan
        ], 201);
    }

    function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'jurusan' => 'required'
        ]);

        if($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $data = [
            'jurusan' => $request->jurusan
        ];

        $jurusan = Jurusan::create($data);

        return response()->json([
            'message' => 'Jurusan berhasil dibuat',
            'jurusan' => $jurusan->jurusan
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'jurusan' => 'required'
        ]);

        if($validated->fails()) {
            return response()->json($validated->errors(), 400);
        }

        $jurusan = Jurusan::where('id', $id)->first();
        
        if(!$jurusan) {
            return response()->json([
                'status' => 'not found',
                'message' => 'jurusan tidak ditemukan'
            ], 403);
        }

        $jurusan->jurusan = $request->jurusan;
        $jurusan->save();

        return response()->json([
            'status' => 'update success',
            'data' => $jurusan->jurusan
        ], 201);
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::where('id', $id)->first();
        
        if(!$jurusan) {
            return response()->json([
                'status' => 'not found',
                'message' => 'jurusan tidak ditemukan'
            ], 403);
        }

        $jurusan->delete();

        return response()->json(204);
    }
}
