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
        Schema::create(config('oo-metas.table_names.oo_metas', 'oo_metas'), function (Blueprint $table) {
            $table->id();

            $table->string('model_id')->nullable();
            $table->string('model_type')->nullable();

            $table->string('connected_id')->nullable();
            $table->string('connected_type')->nullable();

            $table->string('key')->index();
            $table->json('value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('oo-metas.table_names.oo_metas', 'oo_metas'));
    }
};
