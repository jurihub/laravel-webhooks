<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebhooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('type');
            $table->string('target');
            $table->mediumText('data');
            $table->boolean('is_working')->default(false)->index();
            $table->boolean('is_closed')->default(false)->index();
            $table->timestamp('closed_at')->nullable()->default(null);
            $table->integer('nb_tries')->default(0);
            $table->timestamp('last_tried_at')->nullable()->default(null);
            $table->string('status')->nullable()->default(null)->index()->comment("canceled, failed, success");
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhooks');
    }
}
