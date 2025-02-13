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
        Schema::create('sd_table_joins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_database_table_id')->constrained();

            $table->string('type');

            $table->foreignId('service_database_table_column_id')->constrained();

            $table->string('from_column');
            $table->string('to_column');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sd_table_joins');
    }
};
