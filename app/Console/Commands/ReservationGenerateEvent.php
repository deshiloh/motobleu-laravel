<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ReservationGenerateEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservation:event {reservation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param GoogleCalendarService $calendarService
     * @return int
     */
    public function handle(GoogleCalendarService $calendarService): int
    {
        $reservation = $this->argument("reservation");

        $this->info("Création de la réservation dans Google Calendar...");

        $reservation = Reservation::find($reservation);

        if (!$reservation) {
            $this->error("La réservation n'existe pas.");

            return CommandAlias::FAILURE;
        }

        $calendarService
            ->createEventForMotobleu($reservation);

        $calendarService
            ->createEventForSecretary($reservation);

        $this->info("Opération réussite.");
        return CommandAlias::SUCCESS;
    }
}
