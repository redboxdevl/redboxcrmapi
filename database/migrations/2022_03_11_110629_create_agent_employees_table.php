<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companyId');
            $table->string('title');
            $table->string('name');
            $table->string('shortname');
            $table->string('fathername');
            $table->date('dob');
            $table->enum('maritalStatus', ['Single', 'Married','Divorce','Widow']);
            $table->string('phoneNo');
            $table->string('MobileNo');
            $table->string('DeptName');
            $table->string('Designation');
            $table->string('Email');
            $table->string('Password');
            $table->date('JoiningDate');
            $table->string('profilePic');
            $table->enum('Status', ['Approved', 'Pending','Deactive']);
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
        Schema::dropIfExists('agent_employees');
    }
}
