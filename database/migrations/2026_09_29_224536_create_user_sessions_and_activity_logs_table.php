<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->timestamp('last_active_at');
            $table->timestamps();
        });

        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); 
            $table->string('ip_address', 45);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_activity_logs');
    }
};