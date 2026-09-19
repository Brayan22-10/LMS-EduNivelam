<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function reports()
    {
        $courses = [
            [
                'title' => 'Álgebra y Pensamiento Lógico',
                'category' => 'Matemáticas',
                'description' => 'Domina ecuaciones, despejes y resolución de problemas lógicos esenciales para cálculo universitario.',
                'level' => 'Nivelatorio - Básico',
                'duration' => '4 Semanas',
                'badge_color' => 'bg-primary'
            ],
            [
                'title' => 'Comprensión y Análisis de Textos',
                'category' => 'Lectura Crítica',
                'description' => 'Aprende a desglosar textos científicos y ensayos complejos con metodologías de argumentación avanzada.',
                'level' => 'Nivelatorio - Intermedio',
                'duration' => '3 Semanas',
                'badge_color' => 'bg-success'
            ],
            [
                'title' => 'Gestión del Tiempo y Liderazgo',
                'category' => 'Habilidades Blandas',
                'description' => 'Organiza tu agenda universitaria, evita la procrastinación y potencia tu trabajo en equipo.',
                'level' => 'Transición Universitaria',
                'duration' => '2 Semanas',
                'badge_color' => 'bg-warning text-dark'
            ]
        ];

        return view('page.courses', compact('courses'));
    }

    public function directory()
    {
        $instructors = [
            [
                'name' => 'Dr. Alberto Martínez',
                'role' => 'Director del Área de Matemáticas',
                'specialty' => 'Cálculo y Pensamiento Lógico',
                'bio' => 'Magíster en Matemáticas Aplicadas con más de 10 años de experiencia facilitando la transición al cálculo universitario.',
                'badge' => 'Matemáticas',
                'badge_color' => 'bg-primary'
            ],
            [
                'name' => 'Dra. Sofía Rincón',
                'role' => 'Coordinadora de Lectura Crítica',
                'specialty' => 'Análisis de Textos y Argumentación',
                'bio' => 'Doctora en Filología y experta en comprensión de textos académicos complejos y redacción científica.',
                'badge' => 'Lectura Crítica',
                'badge_color' => 'bg-success'
            ],
            [
                'name' => 'Lic. Carlos Gómez',
                'role' => 'Especialista en Habilidades Blandas',
                'specialty' => 'Liderazgo y Gestión del Tiempo',
                'bio' => 'Psicólogo educativo enfocado en el desarrollo de competencias de inteligencia emocional y trabajo en equipo para universitarios.',
                'badge' => 'Habilidades Blandas',
                'badge_color' => 'bg-warning text-dark'
            ]
        ];

        return view('page.instructors', compact('instructors'));
    }
}