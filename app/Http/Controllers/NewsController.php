<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::latest()->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:publicado,borrador',
        ], [
            'title.required' => 'El título del aviso es obligatorio.',
            'category.required' => 'La categoría es obligatoria.',
            'content.required' => 'El contenido detallado de la noticia no puede estar vacío.',
            'status.required' => 'El estado de publicación es obligatorio.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.max' => 'La imagen no debe pesar más de 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news-images', 'public');
        }

        News::create([
            'title' => $request->title,
            'category' => $request->category,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imagePath,
            'status' => $request->status,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.news.index')->with('success', '¡Aviso o noticia guardado exitosamente!');
    }

    public function publicIndex()
    {
        $news = News::where('status', 'publicado')->latest()->paginate(9);
        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status' => 'required|in:publicado,borrador',
        ], [
            'title.required' => 'El título del aviso es obligatorio.',
            'category.required' => 'La categoría es obligatoria.',
            'content.required' => 'El contenido detallado de la noticia no puede estar vacío.',
            'status.required' => 'El estado de publicación es obligatorio.',
        ]);

        $imagePath = $news->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('news-images', 'public');
        }

        $news->update([
            'title' => $request->title,
            'category' => $request->category,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imagePath,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.news.index')->with('success', '¡Noticia actualizada correctamente!');
    }

    public function destroy(News $news)
    {
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();
        return redirect()->route('admin.news.index')->with('success', '¡Noticia eliminada correctamente!');
    }
}