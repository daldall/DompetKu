<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->latest()->get();

        return view('kategori.index', compact('categories'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'ikon'          => 'required|string|max:50',
            'warna'         => 'required|in:success,danger',
        ]);

        Category::create([
            'user_id'       => Auth::id(),
            'nama_kategori' => $request->nama_kategori,
            'ikon'          => $request->ikon,
            'warna'         => $request->warna,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $kategori)
    {
        if ($kategori->user_id !== Auth::id()) abort(403);

        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Category $kategori)
    {
        if ($kategori->user_id !== Auth::id()) abort(403);

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'ikon'          => 'required|string|max:50',
            'warna'         => 'required|in:success,danger',
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
            'ikon'          => $request->ikon,
            'warna'         => $request->warna,
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $kategori)
    {
        if ($kategori->user_id !== Auth::id()) abort(403);

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
