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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('emp_code', 200)->unique()->nullable();
            $table->string('emp_name', 200)->nullable();
            $table->string('email', 200)->unique()->nullable();
            $table->string('password', 200)->nullable();
            $table->string('dob', 200)->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed', 'Separated']);
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('joining_date', 200)->nullable();
            $table->foreignId('company_id')->constrained('company');
            $table->string('phone_number', 200)->nullable();
            $table->string('mobile_number', 200)->nullable();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('designation_id')->constrained('designations');
            $table->foreignId('branch_id')->constrained('branches');
            $table->string('status', 200)->nullable();
            $table->string('profile_pic', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
