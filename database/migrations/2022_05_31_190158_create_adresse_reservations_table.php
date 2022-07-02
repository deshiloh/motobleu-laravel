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
        Schema::create('adresse_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('adresse');
            $table->string('adresse_complement')->nullable();
            $table->string('code_postal');
            $table->string('ville');

            $table->foreignId('user_id')
                ->nullable(true)
                ->constrained('users');

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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('adresse_reservations');
        Schema::enableForeignKeyConstraints();
    }
};
