<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraans = Kendaraan::all();
        return view('admin.kendaraan', compact('kendaraans'));
    }

    public function create()
    {
        return view('admin.kendaraan_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required',
            'kapasitas' => 'required|integer|min:1',
            'harga' => 'required|numeric',
            'tipe' => 'required',
            'gambar' => 'nullable|image|max:2048',
            'fasilitas' => 'nullable|string',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('gambar_kendaraan', 'public');
        }

        $seats = collect(range(1, $request->kapasitas))
            ->reject(fn($s) => in_array($s, [4, 13]))->values()->all();

        Kendaraan::create([
            'jenis' => $request->jenis,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
            'tipe' => $request->tipe,
            'gambar' => $gambar,
            'fasilitas' => $request->fasilitas ?? 'AC, Audio',
            'available_seats' => $seats,
        ]);

        return redirect()->route('admin.kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }
}
