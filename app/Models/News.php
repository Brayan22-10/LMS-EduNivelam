<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'excerpt',
        'content',
        'image',
        'status',
        'scheduled_at',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}