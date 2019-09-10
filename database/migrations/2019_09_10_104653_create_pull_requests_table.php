<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePullRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('external_id');
            $table->string('author_id');
            $table->integer('merged_by_id')->nullable();
            $table->string('repository');
            $table->string('branch');
            $table->string('title');
            $table->tinyInteger('comment_count');
            $table->tinyInteger('task_count');
            $table->string('url');
            $table->dateTime('merged_at')->nullable();
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
        Schema::dropIfExists('pull_requests');
    }
}
