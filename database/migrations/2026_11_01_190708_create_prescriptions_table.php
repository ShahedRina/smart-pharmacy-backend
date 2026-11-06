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
    Schema::create('prescriptions', function (Blueprint $table) {
        $table->id();

        $table->foreignId('customer_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->foreignId('family_member_id')
            ->nullable()
            ->constrained('family_members')
            ->nullOnDelete();

        $table->foreignId('pharmacist_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->string('image_url', 255);
        $table->string('status');
        $table->text('review_notes')->nullable();

        $table->timestamp('created_at')->useCurrent();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
