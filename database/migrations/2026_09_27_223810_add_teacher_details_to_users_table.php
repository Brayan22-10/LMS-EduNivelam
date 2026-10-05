<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'dni')) {
                $table->string('dni')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'country_code')) {
                $table->string('country_code')->nullable()->after('dni');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->unique()->after('country_code');
            }
            if (!Schema::hasColumn('users', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'teaching_type')) {
                $table->string('teaching_type')->nullable()->after('birthdate'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [];
            foreach (['dni', 'country_code', 'phone', 'birthdate', 'teaching_type'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columns[] = $column;
                }
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};