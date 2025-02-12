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
        Schema::create('service_database_table_clauses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_database_table_id')->onDelete('cascade');;

            $table->boolean('type')->default(0);
            $table->string('clause');
            $table->string('field');
            $table->string('operator');
            $table->string('value');

            // $table->string('condition');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_database_table_clauses');
    }
};
