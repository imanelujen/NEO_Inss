<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateLogementsTable extends Migration
{
    public function up()
    {
        Schema::create('logements', function (Blueprint $table) {
            $table->id();
            $table->enum('housing_type', ['APPARTEMENT', 'MAISON', 'PAVILLON','STUDIO','LOFT','VILLA']);
            $table->decimal('surface_area', 8, 2);
            $table->decimal('housing_value', 10, 2);
            $table->integer('construction_year');
            $table->string('ville');
            $table->string('rue');
            $table->string('code_postal');
            $table->enum('occupancy_status', ['Locataire', 'Propriétaire occupant', 'Propriétaire non-occupant']);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('logements');
    }
}
