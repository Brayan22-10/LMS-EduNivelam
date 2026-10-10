<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_identifier' => 'required|string|in:home,about,news,courses,instructors,contact|unique:banners',
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('banners', 'public');
        }

        Banner::create([
            'page_identifier' => $request->page_identifier,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', '¡Banner creado exitosamente!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'page_identifier' => 'required|string|in:home,about,news,courses,instructors,contact|unique:banners,page_identifier,' . $banner->id,
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $banner->image_path;
        if ($request->hasFile('image_path')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image_path')->store('banners', 'public');
        }

        $banner->update([
            'page_identifier' => $request->page_identifier,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', '¡Banner actualizado correctamente!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path && Storage::disk('public')->exists($banner->image_path)) {
            Storage::disk('public')->delete($banner->image_path);
        }

        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', '¡Banner eliminado correctamente!');
    }
}