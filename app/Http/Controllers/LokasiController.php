<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    // Halaman admin - tampilkan semua lokasi
    public function index()
    {
        $lokasis = Location::all();
        return view('admin.lokasi', compact('lokasis'));
    }

    // Halaman admin - form tambah
    public function create()
    {
        return view('admin.lokasi_create');
    }

    // Proses tambah lokasi
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Location::create([
            'name' => $request->name,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    // Halaman edit
    public function edit($id)
    {
        $lokasi = Location::findOrFail($id);
        return view('admin.lokasi_edit', compact('lokasi'));
    }

    // Proses update
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $lokasi = Location::findOrFail($id);
        $lokasi->update([
            'name' => $request->name,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    // Hapus lokasi
    public function destroy($id)
    {
        $lokasi = Location::findOrFail($id);
        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    // API - Ambil berdasarkan ID
    public function show($id)
    {
        $location = Location::find($id);
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        return response()->json($location);
    }

    // API - Ambil berdasarkan nama
    public function showByName($name)
    {
        $location = Location::where('name', $name)->first();
        if (!$location) {
            return response()->json(['message' => 'Location not found'], 404);
        }
        return response()->json($location);
    }
}
