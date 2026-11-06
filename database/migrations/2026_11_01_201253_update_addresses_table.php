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
    Schema::table('addresses', function (Blueprint $table) {

        $table->renameColumn('user_id', 'account_id');

        $table->renameColumn('street', 'details');

        $table->renameColumn('latitude', 'lat');

        $table->renameColumn('longitude', 'lng');

        $table->string('title', 50)->nullable(false)->change();

        $table->decimal('lat', 9, 6)->nullable()->change();

        $table->decimal('lng', 9, 6)->nullable()->change();

        $table->boolean('is_default')->default(false);

        $table->dropColumn([
            'building_number',
            'additional_details',
            'updated_at'
        ]);
    });
}

    /**
     * Reverse the migrations.
     */
 public function down(): void
{
    Schema::table('addresses', function (Blueprint $table) {

        $table->renameColumn('account_id', 'user_id');

        $table->renameColumn('details', 'street');

        $table->renameColumn('lat', 'latitude');

        $table->renameColumn('lng', 'longitude');

        $table->string('title')->nullable()->change();

        $table->decimal('latitude', 10, 7)->nullable()->change();

        $table->decimal('longitude', 10, 7)->nullable()->change();

        $table->dropColumn('is_default');

        $table->string('building_number')->nullable();
        $table->text('additional_details')->nullable();
        $table->timestamp('updated_at')->nullable();
    });
}
};
