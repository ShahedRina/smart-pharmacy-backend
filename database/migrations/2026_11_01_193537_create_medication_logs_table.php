<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('medication_logs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('schedule_id')
            ->constrained('medication_schedules')
            ->cascadeOnDelete();

        $table->foreignId('logged_by')
            ->constrained('users');

        $table->dateTime('scheduled_at');

        $table->string('status');

        $table->dateTime('logged_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    Schema::dropIfExists('medication_logs');
}
};
