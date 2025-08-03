<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateDevisAutoTable extends Migration
{
    public function up()
    {
        Schema::create('devis_auto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_devis')->constrained('devis')->onDelete('cascade');
            $table->foreignId('id_vehicule')->constrained('vehicules')->onDelete('cascade');
            $table->foreignId('id_conducteur')->constrained('conducteurs')->onDelete('cascade');
            $table->json('formules_choisis');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('devis_auto');
    }
}
