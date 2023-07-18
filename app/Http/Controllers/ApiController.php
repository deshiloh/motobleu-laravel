<?php

namespace App\Http\Controllers;

use App\Events\ReservationConfirmed;
use App\Http\Resources\ReservationResource;
use App\Mail\PiloteAttached;
use App\Mail\PiloteDetached;
use App\Models\Pilote;
use App\Models\Reservation;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
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
        $reservation->statut == \App\Enum\ReservationStatus::Confirmed;
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
