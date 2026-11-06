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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();

        $table->foreignId('customer_id')
            ->constrained('users');

        $table->foreignId('family_member_id')
            ->nullable()
            ->constrained('family_members')
            ->nullOnDelete();

        $table->foreignId('pharmacist_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->foreignId('delivery_agent_id')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->foreignId('prescription_id')
            ->nullable()
            ->constrained('prescriptions')
            ->nullOnDelete();

        $table->foreignId('address_id')
            ->constrained('addresses');

        $table->dateTime('order_date');
        $table->decimal('total_amount', 10, 2);
        $table->string('status');
        $table->string('delivery_otp', 6)->nullable();
        $table->string('recipient_name', 120);
        $table->dateTime('delivered_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
