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
    Schema::create('notifications', function (Blueprint $table) {
        $table->id();

        $table->foreignId('account_id')
            ->constrained('users');

        $table->string('type');

        $table->string('title', 150);

        $table->text('body');

        $table->integer('reference_id')->nullable();

        $table->boolean('is_read')->default(false);

        $table->dateTime('created_at');
    });
}

    /**
     * Reverse the migrations.
     */
  public function down(): void
{
    Schema::dropIfExists('notifications');
}
};
