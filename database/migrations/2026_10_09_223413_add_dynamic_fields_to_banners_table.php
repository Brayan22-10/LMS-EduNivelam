<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'badge_text')) {
                $table->string('badge_text')->nullable(); 
            }
            if (!Schema::hasColumn('banners', 'content_text')) {
                $table->text('content_text')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['badge_text', 'content_text']);
        });
    }
};