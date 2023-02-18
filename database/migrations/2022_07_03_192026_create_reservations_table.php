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

            $table->integer('statut')->default(\App\Enum\ReservationStatus::Created->value);
            $table->string('commande')->nullable();
            $table->string('reference')->nullable();
            $table->string('pickup_origin')->nullable();
            $table->string('drop_off_origin')->nullable();
            $table->string('event_id')->nullable();
            $table->string('event_secretary_id')->nullable();
            $table->longText('comment')->nullable();

            $table->float('tarif')->nullable();
            $table->float('majoration')->nullable();
            $table->float('complement')->nullable();

            $table->float('tarif_pilote')->nullable();
            $table->float('majoration_pilote')->nullable();
            $table->float('encompte_pilote')->nullable();
            $table->float('encaisse_pilote')->nullable();

            $table->longText('comment_facture')->nullable();
            $table->longText('comment_pilote')->nullable();

            $table->boolean('send_to_passager')->default(true);
            $table->boolean('send_to_user')->default(true);
            $table->boolean('has_back')->default(false);
            $table->boolean('calendar_passager_invitation')->default(true);
            $table->boolean('calendar_user_invitation')->default(true);

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

            $table->foreignId('entreprise_id')
                ->nullable(true)
                ->constrained('entreprises');

            $table->timestamps();

            $table->index('reference');
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
