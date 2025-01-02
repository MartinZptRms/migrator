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
        Schema::create('service_database_table_columns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_database_table_id')->constrained()->onDelete('cascade');;

            $table->foreignId('column_id')->constrained('columns','id');
            $table->foreignId('custom_column_id')->nullable()->constrained('columns','id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_database_table_columns');
    }
};
