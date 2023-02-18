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
        Schema::create('pilotes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone');
            $table->string('email');
            $table->string('entreprise')->nullable();
            $table->string('adresse')->nullable();
            $table->string('adresse_complement')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->boolean('is_actif')->default(true);
            $table->timestamps();

            $table->index('nom');
            $table->index('prenom');
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
        Schema::dropIfExists('pilotes');
        Schema::enableForeignKeyConstraints();
    }
};
