<?php

namespace App\Http\Controllers;

use App\Enum\ReservationStatus;
use App\Events\ReservationConfirmed;
use App\Http\Resources\ReservationResource;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Models\Pilote;
use App\Models\Reservation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * @param Reservation $reservation
     * @return JsonResponse
     */
    public function confirmationAction(Request $request, Reservation $reservation): JsonResponse
    {
        // Vérification que le pilote est bien renseigné dans la request.
        if (!$request->has('pilote')) {
            return new JsonResponse([
                "message" => "Le pilote n'est pas renseigné."
            ], 400);
        }

        if (!$request->has('message')) {
            return new JsonResponse([
                'message' => 'Le message de l\'email est manquant'
            ], 400);
        }

        // On vérifie que le pilote existe bien dans la base de données
        try {
            $pilote = Pilote::findOrFail($request->post('pilote'));
        } catch (Exception) {
            return new JsonResponse([
                "message" => "Pilote non trouvé"
            ], 404);
        }

        $reservation->statut = ReservationStatus::Confirmed->value;
        $reservation->pilote_id = $pilote->id;
        $reservation->encaisse_pilote = $request->post('encaisse');
        $reservation->encompte_pilote = $request->post('encompte');
        $reservation->comment_pilote = $request->post('commentPilote');

        $reservation->update();

        try {
            \Mail::to($reservation->pilote->email)
                ->send(new PiloteAttached($reservation));
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("Erreur pendant la génération Google Calendar", [
                    'exception' => $exception,
                    'reservation' => $reservation
                ]);
            }
        }

        ReservationConfirmed::dispatch($reservation, $request->post('message'));

        return new JsonResponse([
            'message' => "réservation confirmée"
        ], 200);
    }

    public function handleAction(Request $request, Reservation $reservation, string $action)
    {
        if ($request->has('pilote')) {
            try {
                $pilote = Pilote::findOrFail($request->post('pilote'));
            } catch (Exception) {
                return new JsonResponse([
                    "message" => "Pilote non trouvé"
                ], 404);
            }
        }

        $reservation->pilote_id = $pilote->id;
        $reservation->encaisse_pilote = $request->post("encaisse");
        $reservation->encompte_pilote = $request->post("encompte");
        $reservation->comment_pilote = $request->post("comment_pilote");

        switch ($action) {
            case "confirmation":
                if (!$request->has('message')) {
                    return new JsonResponse([
                        "message" => "message manquant"
                    ], 400);
                }

                try {
                    $this->handleConfirmation($reservation, $request->post('message'));
                } catch (Exception) {
                    return new JsonResponse([
                        "message" => "Une erreur s'est produite pendant la confirmation de la réservation"
                    ], 500);
                }


                break;
            case "update-pilote":
                try {
                    $this->handleUpdatePilote($reservation);
                } catch (Exception) {
                    return new JsonResponse([
                        "message" => "Une erreur s'est produite pendant la mise à jour du pilote"
                    ], 500);
                }

                break;
            default:
                return new JsonResponse([
                    "message" => "action inconnue"
                ], 400);
        }

        $reservation->update();

        return new ReservationResource($reservation);
    }

    private function handleConfirmation(Reservation $reservation, string $message)
    {
        $reservation->statut = \App\Enum\ReservationStatus::Confirmed;
        $reservation->refresh();

        \Mail::to($reservation->pilote->email)
            ->send(new PiloteAttached($reservation));

        ReservationConfirmed::dispatch($reservation, $message);
    }

    private function handleUpdatePilote(Reservation $reservation)
    {
        if($reservation->isDirty('pilote_id')) {
            $currentPilote = Pilote::find($reservation->getOriginal('pilote_id'));
            \Mail::to($currentPilote->email)->send(new PiloteDetached($reservation));

            $newPilote = Pilote::find($reservation->pilote_id);
            \Mail::to($newPilote->email)->send(new PiloteAttached($reservation));

            $reservation->pilote()->associate($newPilote);
        }
    }
}
