<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\App;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{

    public function handleEvent(Reservation $reservation)
    {

        // TODO Envoi de l'event secrétaire
        if ($reservation->is_cancel) {
            return false;
        }

        // On évite la création d'évènement lors des tests
        if (!App::environment('prod', 'local')) {
            return false;
        }

        $isNewEvent = empty($reservation->event_id);

        $event = ($isNewEvent) ? new Event() : Event::find($reservation->event_id);

        $event->name = $this->generateTitle($reservation);
        $event->description = $this->generateEventContent($reservation);

        $event->startDateTime = $reservation->pickup_date;
        $event->endDateTime = $reservation->pickup_date->addHour();

        if ($reservation->calendar_user_invitation) {
            $email = App::environment(['local']) ? 'm.alvarez.iglisias@gmail.com' : $reservation->passager->user->email;
            $event->addAttendee([
                'email' => $email
            ]);
        }

        if ($reservation->calendar_passager_invitation) {
            $email = App::environment(['local']) ? 'm.alvarez.iglisias@gmail.com' : $reservation->passager->email;
            $event->addAttendee([
                'email' => $email
            ]);
        }

        $savedEvent = $event->save();

        if ($isNewEvent) {
            $reservation->update([
                'event_id' => $savedEvent->id
            ]);
        }
    }

    public function deleteEvent(Reservation $reservation)
    {
        try {
            if (App::environment(['local', 'prod'])) {
                $event = Event::find($reservation->event_id);
                $event->delete();

                $reservation->updateQuietly([
                    'event_id' => null
                ]);
            }
        }catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $reservation
                ])->exception($exception);
            }
            // TODO Sentry en production
        }
    }

    /**
     * Generate title event
     * @param Reservation $reservation
     * @return string
     */
    private function generateTitle(Reservation $reservation)
    {
        $piloteLabel = $this->generatePiloteLabel($reservation);

        return sprintf(
            'Course n°%s - %s / %s / %s',
            $reservation->reference,
            ucfirst($reservation->passager->user->entreprise->nom),
            $reservation->passager->nom,
            'plt: '. $piloteLabel
        );
    }

    /**
     * Generate content event
     * @param Reservation $reservation
     * @return string
     */
    private function generateEventContent(Reservation $reservation)
    {
        $piloteLabel = $this->generatePiloteLabel($reservation);

        $description = "Société: %s\n";
        $description .= "Passager: %s\n";
        $description .= "Tel passager: %s\n";
        $description .= "Email: %s\n\n";
        $description .= "Assistante: %s\n";
        $description .= "Tel assistante: %s\n";
        $description .= "Email assistante: %s\n\n";
        $description .= "Pilote: %s\n";
        $description .= "Date de la course: %s\n";
        $description .= "Adresse de départ: %s\n";
        $description .= "Provenance / N°: %s\n\n";
        $description .= "Adresse de destination: %s\n";
        $description .= "Destination / N°: %s\n\n";
        $description .= "Commentaires: %s\n\n";
        $description .= "Tarif : \n\n";
        $description .= "\nLien: %s\n";
        $description .= "\n\nMotobleu\n26-28 rue Marius Aufan\n92300 Levallois Perret\nTél: +33647938617\ncontact@motobleu-paris.com\nRCS 824 721 955 NANTERRE"; //company_details

        $phones = implode(' - ', [$reservation->passager->telephone, $reservation->passager->portable]);

        $description = sprintf(
            $description,
            $reservation->passager->user->entreprise->nom,
            $reservation->passager->nom,
            $phones,
            $reservation->passager->email,
            $reservation->passager->user->full_name,
            $reservation->passager->user->telephone,
            $reservation->passager->user->email,
            $piloteLabel,
            $reservation->pickup_date->format('d/m/Y à H\hi'),
            $reservation->display_from,
            $reservation->pickup_origin,
            $reservation->display_to,
            $reservation->drop_off_origin,
            $reservation->comment,
            route('admin.reservations.show', ['reservation' => $reservation->id])
        );

        return $description;
    }

    private function generatePiloteLabel(Reservation $reservation)
    {
        return ($reservation->pilote()->exists()) ? $reservation->pilote->full_name : 'En attente';
    }
}
