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
    Schema::create('user_medications', function (Blueprint $table) {
        $table->id();

        $table->foreignId('account_id')
            ->constrained('users');

        $table->foreignId('family_member_id')
            ->nullable()
            ->constrained('family_members');

        $table->foreignId('medicine_id')
            ->constrained('medicines');

        $table->foreignId('prescription_id')
            ->nullable()
            ->constrained('prescriptions');

        $table->string('dosage', 50);
        $table->date('start_date');
        $table->date('end_date')->nullable();
        $table->integer('qty_on_hand');
        $table->integer('refill_alert_days');

    });
}

    /**
     * Reverse the migrations.
     */
public function down(): void
{
    Schema::dropIfExists('user_medications');
}
};
