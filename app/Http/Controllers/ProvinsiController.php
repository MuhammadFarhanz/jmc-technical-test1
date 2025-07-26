<?php
namespace App\Http\Controllers;

use App\Models\Kabupaten;
use App\Models\Penduduk;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProvinsiController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Provinsi::create($validated);

        return redirect()->back()->with('success', 'Provinsi berhasil ditambahkan!');

    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $provinsi = Provinsi::findOrFail($id);
        $provinsi->update($validated);

        return redirect()->back();
    }

    public function destroy($id)
    {
        $provinsi = Provinsi::findOrFail($id);
        $provinsi->delete();

        return redirect()->back();
    }

}