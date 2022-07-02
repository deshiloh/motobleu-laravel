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
        Schema::create('passagers', function (Blueprint $table) {
            $table->id();

            $table->string('nom');
            $table->string('portable')->nullable();
            $table->string('telephone');
            $table->string('email');

            $table->boolean('is_actif')->default(true);

            $table->foreignId('user_id')
                ->constrained('users');

            $table->foreignId('cost_center_id')
                ->nullable()
                ->constrained('cost_centers')
            ->nullOnDelete();

            $table->foreignId('type_facturation_id')
                ->nullable()
                ->constrained('type_facturations')
            ->nullOnDelete();

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
        Schema::dropIfExists('passager');
    }
};
