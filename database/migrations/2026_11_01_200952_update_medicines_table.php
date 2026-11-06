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
    Schema::table('medicines', function (Blueprint $table) {

        $table->renameColumn('quantity', 'stock_quantity');

        $table->string('name', 150)->change();

        $table->boolean('requires_prescription')->default(false);

        $table->dropColumn(['description', 'image']);
    });
}

    /**
     * Reverse the migrations.
     */
 public function down(): void
{
    Schema::table('medicines', function (Blueprint $table) {

        $table->renameColumn('stock_quantity', 'quantity');

        $table->string('name')->change();

        $table->dropColumn('requires_prescription');

        $table->text('description')->nullable();
        $table->string('image')->nullable();
    });
}
};
