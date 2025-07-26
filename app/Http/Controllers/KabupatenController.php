<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KabupatenController extends Controller
{
    public function index()
    {
        $kabupatens = Kabupaten::with('provinsi')->get();

        return Inertia::render('Kabupaten/Index', [
            'kabupatens' => $kabupatens
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'provinsi_id' => 'required|integer|exists:provinsis,id',
            'jumlah_penduduk' => 'required|integer|min:0',
        ]);
        Kabupaten::create($request->only(['nama', 'provinsi_id', 'jumlah_penduduk']));
        return redirect()->back()->with('success', 'Kabupaten berhasil ditambahkan!');
    }

    public function update(Request $request, Kabupaten $kabupaten)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'provinsi_id' => 'required|integer|exists:provinsis,id',
            'jumlah_penduduk' => 'required|integer|min:0',
        ]);
        $kabupaten->update($request->only(['nama', 'provinsi_id', 'jumlah_penduduk']));
        return redirect()->back();
    }

    public function destroy($id)
    {
        $kabupaten = Kabupaten::findOrFail($id);
        $kabupaten->delete();

        return redirect()->back();
    }

}
