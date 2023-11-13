<?php

namespace App\Services\EventCalendar;

use App\Enum\ReservationStatus;
use App\Models\Pilote;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{

    private EventFactory $eventFactory;

    public function __construct(EventFactory $eventFactory)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * @var Reservation
     */
    private Reservation $reservation;

    /**
     * @var bool
     */
    private bool $forSecretary = false;

    public function createEventForSecretary(Reservation $reservation): bool
    {
        $this->reservation = $reservation;
        $reservation->refresh();
        $this->forSecretary = true;

        if ($this->reservation->statut == ReservationStatus::Canceled) {
            return false;
        }

        $event = $this->eventFactory->getEvent($this->reservation->event_secretary_id);

        $event->name = $this->generateTitle();
        $event->description = $this->generateEventContent();

        $event = $this->generateCommunData($event);

        // Invitation secrétaire
        $email = App::environment(['local', 'beta']) ?
            'm.alvarez.iglisias@gmail.com' :
            $this->reservation->passager->user->email;

        $event->addAttendee([
            'email' => trim($email)
        ]);

        if ($this->reservation->calendar_passager_invitation) {
            $email = App::environment(['local', 'beta']) ?
                'm.alvarez.iglisias@gmail.com' :
                $this->reservation->passager->email;

            $event->addAttendee([
                'email' => trim($email)
            ]);
        }

        $savedEvent = $event->save(null, [
            'sendUpdates' => 'all'
        ]);

        if (empty($this->reservation->event_secretary_id)) {
            $this->reservation->updateQuietly([
                'event_secretary_id' => $savedEvent->id
            ]);
        }
        return true;
    }

    public function createEventForMotobleu(Reservation $reservation): bool
    {
        $this->reservation = $reservation;
        $reservation->refresh();

        if ($this->reservation->statut == ReservationStatus::Canceled) {
            return false;
        }

        $event = $this->eventFactory->getEvent($this->reservation->event_id);

        $event->name = $this->generateTitle();
        $event->description = $this->generateEventContent();

        $event = $this->generateCommunData($event);

        $event->location = $this->reservation->display_from;

        $savedEvent = $event->save();

        if (is_null($this->reservation->event_id)) {
            $this->reservation->updateQuietly([
                'event_id' => $savedEvent->id
            ]);
        }

        return true;
    }

    public function generateCommunData(Event $event): Event
    {
        $event->startDateTime = $this->reservation->pickup_date;

        if ($this->forSecretary) {
            $event->endDateTime = $this->reservation->pickup_date->addMinute();
        } else {
            $event->endDateTime = $this->reservation->pickup_date->addHour();
        }

        return $event;
    }

    public function deleteEvent(Reservation $reservation): bool
    {
        $reservation->refresh();

        try {
            if (App::environment(['local', 'prod', 'beta'])) {
                if (!is_null($reservation->event_id)) {
                    $event = Event::find($reservation->event_id);
                    $event->delete();

                    $reservation->updateQuietly([
                        'event_id' => null,
                    ]);
                }

                if (!is_null($reservation->event_secretary_id)) {
                    $eventSecretary = Event::find($reservation->event_secretary_id);
                    $eventSecretary->delete();

                    $reservation->updateQuietly([
                        'event_secretary_id' => null
                    ]);
                }
            }
        } catch (\Exception $exception) {
            if (App::environment(['local'])) {
                ray([
                    'reservation' => $reservation
                ])->exception($exception);
            }
            Log::channel('sentry')->error("Erreur pendant la suppression de l'évènement", [
                'exception' => $exception,
                'reservation' => $reservation
            ]);
            return false;
        }

        return true;
    }

    /**
     * Generate title event
     * @return string
     */
    private function generateTitle(): string
    {
        if ($this->forSecretary) {
            return sprintf(
                'RESERVATION MOTOBLEU Course n°%s - %s / %s',
                $this->reservation->reference,
                ucfirst($this->reservation->entreprise->nom),
                $this->reservation->passager->nom
            );
        } else {
            $piloteLabel = $this->generatePiloteLabel();
            return sprintf(
                'Course n°%s - %s / %s / %s',
                $this->reservation->reference,
                ucfirst($this->reservation->entreprise->nom),
                $this->reservation->passager->nom,
                'plt: '. $piloteLabel
            );
        }
    }

    /**
     * Generate content event
     * @return string
     */
    private function generateEventContent(): string
    {

        if ($this->forSecretary) {
            $description = "Société: %s\n";
            $description .= "Passager: %s\n";
            $description .= "Tel passager: %s\n";
            $description .= "Email: %s\n\n";
            $description .= "Assistante: %s\n";
            $description .= "Tel assistante: %s\n";
            $description .= "Email assistante: %s\n\n";
            $description .= "Date de la course: %s\n";
            $description .= "Adresse de départ: %s\n";
            $description .= $this->reservation->pickup_origin ?  "Provenance / N°: %s\n\n" : '%s';
            $description .= $this->reservation->has_steps ? "Destinations intermédiaires : %s\n\n" : '%s';
            $description .= "Adresse de destination: %s\n";
            $description .= $this->reservation->drop_off_origin ?  "Destination / N°: %s\n\n" : '%s';
            $description .= "Commentaires: %s\n\n";
            $description .= "\nLien: %s\n";
            $description .= "\n\nMotobleu\n26-28 rue Marius Aufan\n92300 Levallois Perret\nTél: +33647938617\ncontact@motobleu-paris.com\nRCS 824 721 955 NANTERRE"; //company_details

            $phones = implode(' - ', [$this->reservation->passager->telephone, $this->reservation->passager->portable]);

            return sprintf(
                $description,
                $this->reservation->entreprise->nom,
                $this->reservation->passager->nom,
                $phones,
                $this->reservation->passager->email,
                $this->reservation->passager->user->full_name,
                $this->reservation->passager->user->telephone,
                $this->reservation->passager->user->email,
                $this->reservation->pickup_date->format('d/m/Y à H\hi'),
                $this->reservation->display_from,
                $this->reservation->pickup_origin ?? '',
                $this->reservation->has_steps ? $this->reservation->steps : '',
                $this->reservation->display_to,
                $this->reservation->drop_off_origin ?? '',
                $this->reservation->comment,
                route('admin.reservations.show', ['reservation' => $this->reservation->id])
            );
        } else {
            $piloteLabel = $this->generatePiloteLabel();

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
            $description .= $this->reservation->pickup_origin ? "Provenance / N°: %s\n\n" : '%s';
            $description .= $this->reservation->has_steps ? "Destinations intermédiaires : %s\n\n" : '%s';
            $description .= "Adresse de destination: %s\n";
            $description .= $this->reservation->drop_off_origin ? "Destination / N°: %s\n\n" : '%s';
            $description .= "Commentaires: %s\n\n";
            $description .= "Tarif : %s€%s \n\n";
            $description .= "\nLien: %s\n";
            $description .= "\n\nMotobleu\n26-28 rue Marius Aufan\n92300 Levallois Perret\nTél: +33647938617\ncontact@motobleu-paris.com\nRCS 824 721 955 NANTERRE"; //company_details

            $phones = implode(' - ', [$this->reservation->passager->telephone, $this->reservation->passager->portable]);

            $tarifLabelle = ($this->reservation->encompte_pilote > 0) ? "/com15 EN COMPTE" : "/com15 A ENCAISSER CB";
            $tarifValue = ($this->reservation->encompte_pilote > 0) ?
                $this->reservation->encompte_pilote :
                $this->reservation->encaisse_pilote;

            return sprintf(
                $description,
                $this->reservation->entreprise->nom,
                $this->reservation->passager->nom,
                $phones,
                $this->reservation->passager->email,
                $this->reservation->passager->user->full_name,
                $this->reservation->passager->user->telephone,
                $this->reservation->passager->user->email,
                $piloteLabel,
                $this->reservation->pickup_date->format('d/m/Y à H\hi'),
                $this->reservation->display_from,
                $this->reservation->pickup_origin ?? '',
                $this->reservation->has_steps ? $this->reservation->steps : '',
                $this->reservation->display_to,
                $this->reservation->drop_off_origin ?? '',
                $this->reservation->comment,
                $tarifValue,
                $tarifLabelle,
                route('admin.reservations.show', ['reservation' => $this->reservation->id])
            );
        }
    }

    private function generatePiloteLabel(): string
    {
        $this->reservation->refresh();
        return ($this->reservation->pilote instanceof Pilote) ? $this->reservation->pilote->full_name : 'En attente';
    }
}
