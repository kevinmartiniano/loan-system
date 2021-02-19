<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->integer('from_wallet_id');
            $table->integer('to_wallet_id');

            $table->foreign('from_wallet_id')
                    ->references('id')->on('wallets')
                    ->onDelete('restrict');

            $table->foreign('to_wallet_id')
                    ->references('id')->on('wallets')
                    ->onDelete('restrict');
        });
    }
}
