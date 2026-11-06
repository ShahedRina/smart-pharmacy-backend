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
    Schema::create('medication_schedules', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_medication_id')
            ->constrained('user_medications')
            ->cascadeOnDelete();

        $table->time('dose_time');

        $table->set('days_of_week', [
            'saturday',
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('medication_schedules');
}
};
