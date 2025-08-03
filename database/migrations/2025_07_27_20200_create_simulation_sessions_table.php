<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('simulation_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_dernier_acces')->nullable();
            $table->json('donnees_temporaires')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simulation_sessions');
    }
};
