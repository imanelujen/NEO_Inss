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
        Schema::create('appointments', function (Blueprint $table) {  
        $table->unsignedBigInteger('client_id');
        $table->unsignedBigInteger('devis_habitation_id');
        $table->date('appointment_date');
        $table->time('appointment_time');
        $table->string('status')->default('pending'); // pending, confirmed, cancelled
        $table->timestamps();

        $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        $table->foreign('devis_habitation_id')->references('id')->on('devis_habitation')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
