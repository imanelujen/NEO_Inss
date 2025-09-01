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
        Schema::table('devis_habitation', function (Blueprint $table) {
             $table->json('calculation_factors')->nullable()->after('formules_choisis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devis_habitation', function (Blueprint $table) {
            $table->dropColumn('calculation_factors');
        });
    }
};
