<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadsAssignDatabase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('LeadsAssignDatabase', function (Blueprint $table) {

            $table->id();
            $table->integer('agentid');
            $table->integer('leadid');
            $table->enum('shiftStatus', ['0', '1','2']);
            $table->dateTime('publishAgentDate');
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
        Schema::dropIfExists('LeadsAssignDatabase');
    }
}
