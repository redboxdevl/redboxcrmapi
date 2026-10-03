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
        Schema::create('videorequests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('emp_id')->constrained('employees');
            $table->foreignId('video_category_id')->constrained('video_category');
            $table->foreignId('project_id')->constrained('projects');
            $table->string('video_format', 200)->nullable();
            $table->string('video_topic', 200)->nullable();
            $table->string('guest_name', 200)->nullable();
            $table->string('other_category', 200)->nullable();
            $table->string('gender', 100)->nullable();
            $table->string('age_group', 100)->nullable();
            $table->string('education', 200)->nullable(); // dropdown will show values from db table
            $table->string('location', 200)->nullable(); // dropdown will show values from db table
            $table->string('video_outline', 500)->nullable();
            $table->string('video_instructions', 500)->nullable();
            $table->string('video_ref_image', 900)->nullable();
            $table->string('video_ref_ppt', 900)->nullable();
            $table->string('video_ref_pdf', 900)->nullable();
            $table->string('video_ref_web', 900)->nullable();
            $table->string('video_ref_video', 900)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videorequests');
    }
};
