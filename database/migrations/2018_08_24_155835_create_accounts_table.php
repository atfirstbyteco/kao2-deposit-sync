<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_no',20)->index();
            $table->string('account_name',50)->index();
            $table->enum('account_type',['scbdeposit','scbpromptpay','otherdeposit','otherpromptpay','sms'])->index();
            $table->boolean('account_autosync')->default(true)->index();
            $table->decimal('account_balance',14,2)->default(0.00);
            $table->json('account_options')->nullable()->default('{}');
            $table->boolean('active')->nullable()->default(true)->index();
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
        Schema::dropIfExists('accounts');
    }
}
