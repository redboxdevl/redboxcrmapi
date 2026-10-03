<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadsImportDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LeadsImportDatabase', function (Blueprint $table) {

            $table->id();
            $table->string('clientName');
            $table->string('clientPhone');
            $table->string('clientemail');
            $table->string('clientProject');
            $table->string('clientDesignation');
            $table->string('nature');
            $table->enum('leadStatus', ['active', 'inactive']);
            $table->string('leadType');
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
        //
        Schema::dropIfExists('LeadsImportDatabase');
    }
}
