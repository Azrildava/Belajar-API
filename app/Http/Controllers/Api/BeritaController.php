<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Validator;
use Str;

class BeritaController extends Controller
{

    public function index()
    {
        $berita = Berita::with('kategori', 'tag', 'user')->latest()->get();
        return response()->json([
            'succes' => true,
            'message' => 'daftar berita',
            'data' => $berita,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'judul' => 'required|unique:beritas',
            'deskripsi' => 'required',
            'foto' => 'required|image|mimes:png,jpg|max:2048',
            'id_kategori' => 'required',
            'tag' => 'required|array',
            'id_user' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'data' => $validator->errors(),
            ], 422);
        }

        try {
            $path = $request->File('foto')->store('berita');

            $berita = new Berita;
            $berita->judul = $request->judul;
            $berita->slug = Str::slug($request->judul);
            $berita->deskripsi = $request->deskripsi;
            $berita->foto = $path;
            $berita->id_user = $request->id_user;
            $berita->id_kategori = $request->id_kategori;
            $berita->save();

            //melampirkan banyak tag
            $berita->tag()->attach($request->tag);
            return response()->json([
                'succes' => true,
                'message' => 'data berhasil dibuat',
                'data' => $berita,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'terjadi kesalahan',
                'data' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
