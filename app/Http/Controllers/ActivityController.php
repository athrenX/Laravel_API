<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    // Menampilkan daftar aktivitas di halaman admin (web)
    public function index()
    {
        $activities = Activity::all();
        return view('admin.activities.index', compact('activities'));
    }

    // Menampilkan form tambah aktivitas (web)
    public function create()
    {
        return view('admin.activities.create');
    }

    // Simpan aktivitas baru ke database (web)
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|max:2048', // optional, max 2MB
        ]);

        // Upload gambar jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('activities', 'public');
        }

        // Simpan ke database
        Activity::create([
            'title' => $request->title,
            'category' => $request->category,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.activities.index')
                         ->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    // Form edit aktivitas (web)
    public function edit(Activity $activity)
    {
        return view('admin.activities.edit', compact('activity'));
    }

    // Update aktivitas (web)
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('activities', 'public');
            $activity->image = $imagePath;
        }

        $activity->title = $request->title;
        $activity->category = $request->category;
        $activity->save();

        return redirect()->route('admin.activities.index')
                         ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    // Hapus aktivitas (web)
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('admin.activities.index')
                         ->with('success', 'Aktivitas berhasil dihapus.');
    }

    // API endpoint untuk ambil data aktivitas (untuk Flutter, mobile app, dll)
    public function indexApi()
    {
        $activities = Activity::all();

        // Agar url gambar menjadi lengkap, kita map data manual
        $activitiesTransformed = $activities->map(function ($activity) {
            return [
                'id' => $activity->id,
                'title' => $activity->title,
                'category' => $activity->category,
                'image_url' => $activity->image ? asset('storage/' . $activity->image) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $activitiesTransformed,
        ]);
    }
}
