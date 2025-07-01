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
    Schema::table('datastream', function (Blueprint $table) {
        $table->double('duration')->nullable()->after('energy'); // Atau sesuaikan posisi
    });
}

public function down(): void
{
    Schema::table('datastream', function (Blueprint $table) {
        $table->dropColumn('duration');
    });
}
};
