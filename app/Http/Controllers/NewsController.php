<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        News::where('status', 'programado')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', Carbon::now())
            ->update([
                'status' => 'publicado',
                'scheduled_at' => null
            ]);

        $tab = $request->get('tab', 'all');
        $query = News::latest();

        if ($tab === 'publicado') {
            $query->where('status', 'publicado');
        } elseif ($tab === 'programado') {
            $query->where('status', 'programado');
        } elseif ($tab === 'borrador') {
            $query->where('status', 'borrador');
        }

        $news = $query->paginate(10)->appends(['tab' => $tab]);

        $countAll = News::count();
        $countPublished = News::where('status', 'publicado')->count();
        $countScheduled = News::where('status', 'programado')->count();
        $countDrafts = News::where('status', 'borrador')->count();

        return view('admin.news.index', compact('news', 'tab', 'countAll', 'countPublished', 'countScheduled', 'countDrafts'));
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
            'status' => 'required|in:publicado,borrador,programado', 
            'scheduled_at' => 'nullable',
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

        $scheduledAt = null;
        $status = $request->status;

        if ($request->filled('scheduled_at')) {
            $cleanDate = str_replace('T', ' ', $request->input('scheduled_at'));
            $scheduledAt = Carbon::parse($cleanDate)->format('Y-m-d H:i:s');
            if (Carbon::parse($scheduledAt)->isFuture()) {
                $status = 'programado';
            }
        }

        News::create([
            'title' => $request->title,
            'category' => $request->category,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imagePath,
            'status' => $status,
            'scheduled_at' => $scheduledAt,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('admin.news.index')->with('success', '¡Aviso guardado exitosamente!');
    }

    public function publicIndex()
    {
        News::where('status', 'programado')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', Carbon::now())
            ->update([
                'status' => 'publicado',
                'scheduled_at' => null
            ]);

        $news = News::where('status', 'publicado')
                    ->latest()
                    ->paginate(9);

        return view('news.index', compact('news'));
    }

    public function show(News $news)
    {
        return view('admin.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        $rawDate = $news->getRawOriginal('scheduled_at') ?? $news->scheduled_at;
        
        if ($rawDate) {
            $news->scheduled_at = Carbon::parse($rawDate)->format('Y-m-d\TH:i');
        }

        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:300',
            'status' => 'required|in:publicado,borrador,programado', 
            'scheduled_at' => 'nullable',
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

        $scheduledAt = $news->scheduled_at;

        if ($request->filled('scheduled_at')) {
            $cleanDate = str_replace('T', ' ', $request->input('scheduled_at'));
            $scheduledAt = Carbon::parse($cleanDate)->format('Y-m-d H:i:s');
        }

        $status = $request->status;

        if ($scheduledAt && Carbon::parse($scheduledAt)->isFuture()) {
            $status = 'programado';
        }

        $news->update([
            'title' => $request->title,
            'category' => $request->category,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'image' => $imagePath,
            'status' => $status,
            'scheduled_at' => $scheduledAt,
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