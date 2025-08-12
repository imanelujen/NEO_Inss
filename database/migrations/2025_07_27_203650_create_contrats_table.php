<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateContratsTable extends Migration
{
    public function up()
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->enum('type_contrat',['AUTO','HABITATION']);
            $table->foreignId('id_client')->constrained('clients')->onDelete('cascade');
            $table->foreignId('id_devis')->constrained('devis')->onDelete('cascade');
            $table->foreignId('id_agent')->constrained('agences')->onDelete('cascade');
            $table->foreignId('id_paiement')->nullable()->constrained('paiements')->onDelete('set null');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('prime', 10, 2);
            $table->enum('statut', ['ACTIF', 'SUSPENDU', 'RESILIE', 'EXPIRE']);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('contrats');
    }
}
