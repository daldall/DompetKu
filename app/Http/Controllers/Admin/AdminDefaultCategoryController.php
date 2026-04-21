<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DefaultCategory;
use Illuminate\Http\Request;

class AdminDefaultCategoryController extends Controller
{
    public function index()
    {
        $categories = DefaultCategory::query()->orderByDesc('id')->paginate(10);
        return view('admin.default-kategori.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.default-kategori.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'ikon' => 'required|string|max:50',
            'warna' => 'required|in:success,danger',
        ]);

        DefaultCategory::query()->create($data);

        return redirect()->route('admin.default-kategori.index')->with('success', 'Kategori default berhasil ditambahkan.');
    }

    public function destroy(DefaultCategory $defaultKategori)
    {
        $defaultKategori->delete();
        return redirect()->route('admin.default-kategori.index')->with('success', 'Kategori default berhasil dihapus.');
    }
}
