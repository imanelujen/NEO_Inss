<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateDevisTable extends Migration
{
    public function up()
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->date('date_creation');
            $table->date('date_expiration');
            $table->double('montant_base', 10, 2);
            $table->json('OFFRE_CHOISIE');
            $table->enum('status', [
                'BROUILLON',
                'EN_COURS',
                'FINALISE',
                'ENVOYE',
                'EXPIRE',
                'ACCEPTE',
                'REFUSE'
            ])->default('BROUILLON');

            $table->enum('typedevis', [
                'AUTO',
                'HABITATION',
            ]);

            $table->foreignId('id_simulationsession')->constrained('simulation_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('devis');
    }
}
