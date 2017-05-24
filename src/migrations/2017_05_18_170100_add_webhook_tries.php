<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebhookTries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_tries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('webhook_id')->unsigned()->index();
            $table->integer('response_code')->nullable()->default(null)->index();
            $table->text('response_body')->nullable()->default(null);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
        
        Schema::table('webhook_tries', function (Blueprint $table) {
            $table->foreign('webhook_id')->references('id')->on('webhooks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhook_tries', function (Blueprint $table) {
            $table->dropForeign(['webhook_id']);
        });
        
        Schema::dropIfExists('webhook_tries');
    }
}
