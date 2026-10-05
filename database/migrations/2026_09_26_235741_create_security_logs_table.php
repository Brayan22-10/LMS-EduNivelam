<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status'); 
            $table->string('event_type');
            $table->text('description');
            $table->text('user_agent')->nullable(); 
            $table->timestamps();
            $table->index(['created_at', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
