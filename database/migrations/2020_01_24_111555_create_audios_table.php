<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audios', function (Blueprint $table) {
            $table->bigIncrements('id')->start_from(10000000);
            $table->string('audio_id', 50);
            $table->date('upload_date')->nullable();
            $table->mediumText('title');
            $table->text('description')->nullable();
            $table->integer('file_on_server')->default(1);
            $table->mediumInteger('duration');
            $table->mediumInteger('size')->nullable();
            $table->bigInteger('download_count')->nullable();
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
        Schema::dropIfExists('audios');
    }
}
