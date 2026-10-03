<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerLeadsTblsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_leads_tbls', function (Blueprint $table) {
            $table->id();
            $table->integer('leadid');
            $table->integer('countryid');
            $table->integer('cityid');
            $table->integer('projectid');
            $table->integer('typeid');
            $table->integer('natureid');
            $table->integer('actMediumId');
            $table->string('clientName');
            $table->string('clientStatus');
            $table->enum('Gender',['male','female']);
            $table->string('email');
            $table->integer('mobileNo');
            $table->string('passportCnic');
            $table->integer('whatsappNo');
            $table->string('prefLanguage');
            $table->string('Budget');
            $table->string('timeFrame');
            $table->string('RefferdBy');
            $table->string('clientInterst');
            $table->string('clientActivityDesc');
            $table->enum('LeadStatus',['own','redbox','hot','not interested','Dorment']);
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
        Schema::dropIfExists('customer_leads_tbls');
    }
}
