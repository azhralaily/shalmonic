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
        Schema::create('datastream', function (Blueprint $table) {
            $table->id();
            $table->float('ppm')->nullable();
            $table->float('light_intensity')->nullable();
            $table->float('temp')->nullable();
            $table->float('humid')->nullable();
            $table->float('vpd')->nullable();
            $table->float('batt')->nullable();
            $table->float('current')->nullable();
            $table->float('voltage')->nullable();
            $table->float('power')->nullable();
            $table->float('pf')->nullable();
            $table->float('freq')->nullable();
            $table->float('energy')->nullable();
            $table->string('timestamp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datastream');
    }
};
