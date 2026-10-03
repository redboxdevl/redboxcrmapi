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
        Schema::create('emp_promotions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('emp_id')->constrained('employees');
            $table->foreignId('old_dept_id')->constrained('departments');
            $table->foreignId('new_dept_id')->constrained('departments');
            $table->foreignId('old_designation_id')->constrained('designations');
            $table->foreignId('new_designation_id')->constrained('designations');
            $table->string('old_salary', 200)->nullable();
            $table->string('new_salary', 200)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('promotion_date', 200)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_promotions');
    }
};
