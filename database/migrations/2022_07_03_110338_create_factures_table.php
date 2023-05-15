<?php

use App\Enum\BillStatut;
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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->integer('statut')->default(BillStatut::CREATED->value);
            $table->string('reference')->nullable();
            $table->float('montant_ht')->default(0);
            $table->integer('tva')->default(10);
            $table->string('adresse_client')->nullable();
            $table->string('adresse_facturation')->nullable();
            $table->text('information')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->boolean('is_acquitte')->default(false);
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
        Schema::dropIfExists('factures');
        Schema::enableForeignKeyConstraints();
    }
};
