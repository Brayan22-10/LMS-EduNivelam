<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('category')->default('General'); 
            $table->text('excerpt')->nullable(); 
            $table->string('image')->nullable();
            $table->string('status')->default('publicado')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['category', 'excerpt', 'image']);
        });
    }
};