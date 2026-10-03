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
        Schema::create('emp_leave_applications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->string('from_date', 200)->nullable();
            $table->string('to_date', 200)->nullable();
            $table->string('applied_date', 200)->nullable();
            $table->string('applied_time', 200)->nullable();
            $table->string('description', 200)->nullable();
            $table->string('status', 200)->nullable();
            $table->foreignId('approved_by_id')->constrained('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emp_leave_applications');
    }
};
