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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->string('commande')->nullable();
            $table->string('reference');
            $table->string('pickup_origin')->nullable();
            $table->string('drop_off_origin')->nullable();
            $table->string('event_id')->nullable();
            $table->longText('comment')->nullable();

            $table->float('tarif')->nullable();
            $table->float('majoration')->nullable();
            $table->float('encaisse')->nullable();
            $table->float('encompte')->nullable();
            $table->float('complement')->nullable();
            $table->longText('comment_facture')->nullable();
            $table->longText('comment_pilote')->nullable();

            $table->boolean('send_to_passager')->default(false);
            $table->boolean('send_to_user')->default(false);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancel')->default(false);
            $table->boolean('has_back')->default(false);
            $table->boolean('is_cancel_pay')->default(false);
            $table->boolean('calendar_passager_invitation')->default(false);
            $table->boolean('calendar_user_invitation')->default(false);

            $table->dateTime('pickup_date');

            // Localisation
            $table->foreignId('localisation_from_id')
                ->nullable(true)
                ->constrained('localisations');
            $table->foreignId('localisation_to_id')
                ->nullable(true)
                ->constrained('localisations');

            // AdresseReservation
            $table->foreignId('adresse_reservation_from_id')
                ->nullable(true)
                ->constrained('adresse_reservations');
            $table->foreignId('adresse_reservation_to_id')
                ->nullable(true)
                ->constrained('adresse_reservations');

            $table->foreignId('passager_id')
                ->nullable(true)
                ->constrained('passagers');

            $table->foreignId('pilote_id')
                ->nullable(true)
                ->constrained('pilotes');

            $table->foreignId('reservation_id')
                ->nullable(true)
                ->constrained('reservations');

            $table->foreignId('facture_id')
                ->nullable(true)
                ->constrained('factures');

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

        Schema::dropIfExists('reservations');

        Schema::enableForeignKeyConstraints();
    }
};
