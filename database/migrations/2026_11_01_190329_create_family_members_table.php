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
    Schema::create('family_members', function (Blueprint $table) {
        $table->id();

        $table->foreignId('caregiver_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('linked_account_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->foreignId('address_id')
            ->nullable()
            ->constrained('addresses')
            ->nullOnDelete();

        $table->string('full_name', 120);
        $table->string('relation');
        $table->date('birth_date')->nullable();
        $table->string('phone', 20)->nullable();
        $table->text('special_needs')->nullable();
        $table->boolean('notify_caregiver')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
