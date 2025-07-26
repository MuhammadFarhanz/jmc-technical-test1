<?php

namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PendudukController extends Controller
{
    public function index()
    {
        $penduduks = Penduduk::all();
        return Inertia::render('Penduduk/Index', [
            'penduduks' => $penduduks
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:20|unique:penduduks,nik',
            'umur' => 'required|integer|min:0',
            'alamat' => 'required|string',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
        ]);

        Penduduk::create($validated);

        return redirect()->back()->with('success', 'Penduduk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $penduduk = Penduduk::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:20|unique:penduduks,nik,' . $penduduk->id,
            'umur' => 'required|integer|min:0',
            'alamat' => 'required|string',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
        ]);

        $penduduk->update($validated);

        return redirect()->back()->with('success', 'Data penduduk berhasil diperbarui.');
    }


    public function destroy($id)
    {
        $penduduk = Penduduk::findOrFail($id);
        $penduduk->delete();

        return redirect()->back()
            ->with('success', 'Penduduk berhasil dihapus.');
    }

}