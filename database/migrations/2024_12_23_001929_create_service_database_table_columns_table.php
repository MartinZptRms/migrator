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

            $table->foreignId('service_database_table_id')->constrained();

            $table->foreignId('source_database_table_column_id')->constrained('columns','id','source_database_table_column_id_foreign');
            $table->foreignId('target_database_table_column_id')->constrained('columns','id','target_database_table_column_id_foreign');

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
