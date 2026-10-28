<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    public const PAGES = [
        'home' => [
            'label'  => 'Home',
            'fields' => ['badge_text', 'title', 'subtitle', 'image_path'],
            'help'   => 'Sección Hero del Home (título de bienvenida y tarjeta interactiva/imagen).',
        ],
        'about' => [
            'label'  => 'About',
            'fields' => ['badge_text', 'title', 'subtitle'],
            'help'   => 'Sección Acerca de (etiqueta superior, título principal y párrafo de propósito).',
        ],
        'news' => [
            'label'  => 'News',
            'fields' => ['title', 'subtitle'],
            'help'   => 'Encabezado del feed de Avisos y Novedades Institucionales.',
        ],
        'courses' => [
            'label'  => 'Courses',
            'fields' => ['badge_text', 'title', 'subtitle'],
            'help'   => 'Encabezado del Catálogo de Cursos (etiqueta, título y descripción).',
        ],
        'instructors' => [
            'label'  => 'Instructors',
            'fields' => ['badge_text', 'title', 'subtitle'],
            'help'   => 'Encabezado del Cuerpo Docente (etiqueta, título y descripción).',
        ],
        'contact' => [
            'label'  => 'Contact',
            'fields' => ['title', 'subtitle'],
            'help'   => 'Encabezado de Ayuda y Soporte (título de pregunta y subtítulo de asistencia).',
        ],
    ];

    protected $fillable = [
        'page_identifier',
        'title',
        'subtitle',
        'badge_text',
        'content_text',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function pageKeys(): array
    {
        return array_keys(self::PAGES);
    }

    public static function fieldsFor(string $page): array
    {
        return self::PAGES[$page]['fields'] ?? [];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->image_path
                ? Storage::disk(config('banners.disk'))->url($this->image_path)
                : null;
        });
    }
}