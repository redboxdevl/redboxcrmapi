<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranasferShareLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tranasfer_share_leads', function (Blueprint $table) {
            $table->id();
            $table->integer('leadid');
            $table->integer('fagent');
            $table->integer('tagent');
            $table->string('transferNote');
            $table->string('statusType');
            $table->enum('publishStatus',['pending','approved']);
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
        Schema::dropIfExists('tranasfer_share_leads');
    }
}
