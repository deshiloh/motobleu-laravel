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
        Schema::create('entreprise_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(true);
            $table->unsignedBigInteger('entreprise_id')->nullable(true);

            $table->foreign('entreprise_id')
                ->references('id')
                ->on('entreprises');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('entreprise_user');
        Schema::enableForeignKeyConstraints();
    }
};
