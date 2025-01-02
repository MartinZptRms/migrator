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
        Schema::create('service_database_tables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_database_id')->constrained()->onDelete('cascade');;
            $table->foreignId('table_id')->constrained('tables','id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_database_tables');
    }
};
