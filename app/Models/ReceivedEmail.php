<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'from_email',
        'from_name',
        'subject',
        'body',
        'received_at',
        'is_read',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'is_read' => 'boolean',
    ];
    protected function displayMessage(): Attribute
    {
        return Attribute::get(function () {
            $text = (string) ($this->body ?? '');
            $text = preg_replace('/<br\s*\/?>|<\/(p|div|li|h[1-6])>/i', "\n", $text);
            $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $text = preg_replace("/\r\n|\r/", "\n", $text);
            if (preg_match('/Mensaje\s+detallado\s*:\s*(.+)$/isu', $text, $m)) {
                $text = $m[1];
            }
            $text = preg_replace('/^[ \t]+/m', '', $text);
            $text = preg_replace("/\n{3,}/", "\n\n", $text);

            return trim($text);
        });
    }
}