<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'status' => 'required|in:publicado,borrador',
        ], [
            'title.required' => 'El título del aviso es obligatorio.',
            'category.required' => 'La categoría es obligatoria.',
            'content.required' => 'El contenido detallado de la noticia no puede estar vacío.',
            'status.required' => 'El estado de publicación es obligatorio.',
        ]);

        $imagePath = null;
        if ($request->filled('cropped_image')) {
            $base64Image = $request->input('cropped_image');
            list($type, $base64Image) = explode(';', $base64Image);
            list(, $base64Image)      = explode(',', $base64Image);
            $imageBytes = base64_decode($base64Image);

            $filename = 'news_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put('news-images/' . $filename, $imageBytes);
            $imagePath = 'news-images/' . $filename;
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
            'status' => 'required|in:publicado,borrador',
        ], [
            'title.required' => 'El título del aviso es obligatorio.',
            'category.required' => 'La categoría es obligatoria.',
            'content.required' => 'El contenido detallado de la noticia no puede estar vacío.',
            'status.required' => 'El estado de publicación es obligatorio.',
        ]);

        $imagePath = $news->image;

        if ($request->has('remove_image') && $request->input('remove_image') == '1') {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        if ($request->filled('cropped_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $base64Image = $request->input('cropped_image');
            list($type, $base64Image) = explode(';', $base64Image);
            list(, $base64Image)      = explode(',', $base64Image);
            $imageBytes = base64_decode($base64Image);

            $filename = 'news_' . Str::uuid() . '.jpg';
            Storage::disk('public')->put('news-images/' . $filename, $imageBytes);
            $imagePath = 'news-images/' . $filename;
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