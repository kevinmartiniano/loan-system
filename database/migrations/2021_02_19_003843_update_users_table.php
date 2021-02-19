<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('document', 14)->unique();
            $table->integer('user_type_id');

            $table->foreign('user_type_id')
                    ->references('id')->on('user_types')
                    ->onDelete('restrict');
        });
    }
}
