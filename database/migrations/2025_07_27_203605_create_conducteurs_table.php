<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConducteursTable extends Migration
{
    public function up()
    {
        Schema::create('conducteurs', function (Blueprint $table) {
            $table->id();
            $table->decimal('bonus_malus', 5, 2)->default(1.00); // par exemple : 0.50 (bonus) à 3.50 (malus)
            $table->json('historique_accidents')->nullable();    // texte libre ou JSON
            $table->date('date_obtention_permis');
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
        Schema::dropIfExists('conducteurs');
    }
};
