<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateContratAutoTable extends Migration
{
    public function up()
    {
        Schema::create('contrat_auto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_contrat')->constrained('contrats')->onDelete('cascade');
            $table->foreignId('id_vehicule')->constrained('vehicules')->onDelete('cascade');
            $table->foreignId('id_conducteur')->constrained('conducteurs')->onDelete('cascade');
            $table->json('garanties');
            $table->string('permis_path')->nullable();
            $table->string('cin_recto_path')->nullable();
            $table->string('cin_verso_path')->nullable();
            $table->string('carte_grise_path')->nullable();
            $table->decimal('franchise', 10, 2);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('contrat_auto');
    }
}
