<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_identifier',
        'title',
        'subtitle',
        'badge_text',
        'content_text',
        'image_path',
        'is_active',
    ];
}