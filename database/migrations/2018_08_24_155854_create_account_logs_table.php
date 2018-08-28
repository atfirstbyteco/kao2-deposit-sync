<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->index();
            $table->enum('account_log_type',['debit','credit'])->nullable()->default('debit')->index();
            $table->string('account_log_message',50)->nullable();
            $table->decimal('account_log_change',14,2)->nullable()->default(0.00);
            $table->decimal('account_log_balance',14,2)->nullable()->default(0.00);
            $table->boolean('active')->nullable()->default(1)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_logs');
    }
}
