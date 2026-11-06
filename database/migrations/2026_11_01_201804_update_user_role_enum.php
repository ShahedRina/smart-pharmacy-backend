<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    DB::statement("
        ALTER TABLE users
        MODIFY role ENUM('user', 'admin', 'delivery')
        NOT NULL DEFAULT 'user'
    ");
}

public function down(): void
{
    DB::statement("
        ALTER TABLE users
        MODIFY role ENUM('user', 'pharmacist', 'delivery representative')
        NOT NULL DEFAULT 'user'
    ");
}
};
