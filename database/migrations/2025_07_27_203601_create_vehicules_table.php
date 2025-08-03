<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateVehiculesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicules', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['sedan', 'suv', 'truck', 'motorcycle']);
            $table->string('make');
            $table->string('model');
            $table->enum('fuel_type', ['ESSENCE', 'DIESEL', 'ELECTRIQUE', 'HYBRIDE']);
            $table->integer('tax_horsepower');
            $table->decimal('vehicle_value', 10, 2);
            $table->date('registration_date');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('vehicules');
    }
}
