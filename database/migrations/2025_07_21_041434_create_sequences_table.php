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
        Schema::create(config('sequences.table'), function (Blueprint $table) {
            $table->id();
            $table->string('key', 150);
            $table->string('reset_value', 50)->nullable();
            $table->string('model_type', 150)->nullable();
            $table->string('model_id', 50)->nullable();
            $table->unsignedBigInteger('last_value')->default(0);
            $table->timestamps();

            $table->unique(
                ['key', 'reset_value', 'model_type', 'model_id'],
                'sequence_unique_scope'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('sequences.table'));
    }
};
