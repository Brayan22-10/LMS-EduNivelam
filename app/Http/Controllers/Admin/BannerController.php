<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        if (! in_array($tab, ['all', 'active', 'hidden'], true)) {
            $tab = 'all';
        }

        $query = Banner::query();

        if ($tab === 'active') {
            $query->where('is_active', true);
        } elseif ($tab === 'hidden') {
            $query->where('is_active', false);
        }

        $banners = $query->latest()->paginate(10)->withQueryString();

        $countAll    = Banner::count();
        $countActive = Banner::where('is_active', true)->count();
        $countHidden = $countAll - $countActive;

        return view('admin.banners.index', compact(
            'banners', 'tab', 'countAll', 'countActive', 'countHidden'
        ));
    }

    public function create()
    {
        $usedPages = Banner::pluck('page_identifier')->all();

        return view('admin.banners.create', compact('usedPages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            array_merge([
                'page_identifier' => [
                    'required', 'string',
                    Rule::in(Banner::pageKeys()),
                    Rule::unique('banners', 'page_identifier'),
                ],
            ], $this->fieldRules()),
            $this->messages()
        );

        $page    = $data['page_identifier'];
        $payload = $this->payloadFor($page, $data);

        if ($request->hasFile('image_path') && in_array('image_path', Banner::fieldsFor($page), true)) {
            $payload['image_path'] = $this->storeImage($request);
        }

        Banner::create($payload + [
            'page_identifier' => $page,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', '¡Banner y sección configurados exitosamente!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate($this->fieldRules(), $this->messages());

        $page     = $banner->page_identifier;
        $payload  = $this->payloadFor($page, $data);
        $oldImage = $banner->image_path;

        if ($request->hasFile('image_path') && in_array('image_path', Banner::fieldsFor($page), true)) {
            $payload['image_path'] = $this->storeImage($request);
        }

        $banner->update($payload + [
            'is_active' => $request->boolean('is_active'),
        ]);

        if (isset($payload['image_path']) && $oldImage) {
            $this->disk()->delete($oldImage);
        }

        return redirect()->route('admin.banners.index')
            ->with('success', '¡Banner actualizado correctamente!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            $this->disk()->delete($banner->image_path);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', '¡Banner eliminado correctamente!');
    }

    private function disk()
    {
        return Storage::disk(config('banners.disk'));
    }

    private function storeImage(Request $request): string
    {
        return $request->file('image_path')->store(
            config('banners.directory'),
            config('banners.disk')
        );
    }

    private function fieldRules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:1000'],
            'badge_text' => ['nullable', 'string', 'max:150'],
            'image_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    private function payloadFor(string $page, array $data): array
    {
        $allowed = Banner::fieldsFor($page);

        return [
            'title'      => $data['title'],
            'subtitle'   => in_array('subtitle', $allowed, true) ? ($data['subtitle'] ?? null) : null,
            'badge_text' => in_array('badge_text', $allowed, true) ? ($data['badge_text'] ?? null) : null,
        ];
    }

    private function messages(): array
    {
        return [
            'page_identifier.required' => 'Debes seleccionar una página destino.',
            'page_identifier.in'       => 'La página seleccionada no es válida.',
            'page_identifier.unique'   => 'Esa página ya tiene un banner. Edítalo desde el listado.',
            'title.required'           => 'El título principal es obligatorio.',
            'title.max'                => 'El título no puede superar 255 caracteres.',
            'subtitle.max'             => 'El subtítulo no puede superar 1000 caracteres.',
            'badge_text.max'           => 'La etiqueta no puede superar 150 caracteres.',
            'image_path.image'         => 'El archivo debe ser una imagen.',
            'image_path.mimes'         => 'La imagen debe ser JPG, PNG o WEBP.',
            'image_path.max'           => 'La imagen no puede pesar más de 2 MB.',
        ];
    }
}