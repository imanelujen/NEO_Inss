<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateContratHabitationTable extends Migration
{
    public function up()
    {
        Schema::create('contrat_habitation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contrat')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('id_logement')->constrained('logements')->onDelete('cascade');
            $table->decimal('franchise', 10, 2);
            $table->json('garanties');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('contrat_habitation');
    }
}
