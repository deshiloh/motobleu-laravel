<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressReservationRequest;
use App\Http\Requests\CreatePassengerRequest;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\AdressesReservationResource;
use App\Http\Resources\PassagerResource;
use App\Http\Resources\ReservationResource;
use App\Models\AdresseReservation;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Endpoint qui permet de mettre à jour une réservation
     * @param UpdateReservationRequest $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function updateReservation(
        UpdateReservationRequest $request,
        Reservation $reservation,
        ReservationService $reservationService
    ): JsonResponse {
        $request->validated();

        try {
            $reservationService->updateReservation($reservation, $request->all());
        } catch (Exception $exception) {
            if (App::environment('local')) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('API : Une erreur est survenue pendant la mise à jour de la réservation', [
                    'exception' => $exception,
                    'datas' => $request->all(),
                ]);
            }
        }

        return new JsonResponse([
            'message' => 'La réservation a bien été mise à jour',
            'reservation' => new ReservationResource($reservation)
        ], 200);
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function confirmationAction(Request $request, Reservation $reservation, ReservationService $reservationService): JsonResponse
    {
        $request->validate([
            'pilote_id' => 'required|integer|exists:pilotes,id',
            'message' => 'required|string',
            'comment_pilote' => 'nullable|string',
        ]);

        $pilote = Pilote::find($request->pilote_id);

        $reservationService->confirmReservation(
            $reservation,
            $pilote,
            $request->post('encompte'),
            $request->post('encaisse'),
            $request->post('comment_pilote'),
            $request->post('message')
        );

        return new JsonResponse([
            'message' => "réservation confirmée"
        ], 200);
    }

    /**
     * Met à jour le pilote d'une réservation
     * @param Request $request
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function updatePilote(Request $request, Reservation $reservation, ReservationService $reservationService): JsonResponse
    {
        $request->validate([
            'pilote_id' => 'required|integer|exists:pilotes,id',
            'comment_pilote' => 'nullable|string',
        ]);

        $newPilote = Pilote::find($request->post('pilote_id'));

        try {
            $reservationService->updatePilote(
                $reservation,
                $newPilote,
                $request->post('encompte'),
                $request->post('encaisse'),
                $request->post('comment_pilote')
            );
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('API : Une erreur est survenue pendant la mise à jour du pilote', [
                    'exception' => $exception,
                    'reservation' => $reservation,
                    'old_pilote' => $reservation->pilote,
                    'new_pilote' => $newPilote,
                ]);
            }

            return new JsonResponse([
                'message' => 'Une erreur est survenue pendant la mise à jour du pilote.'
            ], 500);
        }

        return new JsonResponse([
            'message' => 'Pilote mis à jour'
        ], 200);
    }

    /**
     * Permet de modifier le statut de la réservation en annulée, mais facturable.
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function updateStatutCancelledBilled(
        Reservation $reservation,
        ReservationService $reservationService
    ): JsonResponse {
        try {
            $reservationService->updateCancelledBilledStatut($reservation);
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('API : Une erreur est survenue pendant la mise à jour du statut de la réservation en annulée mais facturable', [
                    'exception' => $exception,
                    'reservation' => $reservation
                ]);
            }
        }

        return new JsonResponse([
            'message' => 'Statut correctement mis à jour.'
        ], 200);
    }

    /**
     * Endpoint d'annulation d'une réservation
     * @param Reservation $reservation
     * @param ReservationService $reservationService
     * @return JsonResponse
     */
    public function cancelReservation(Reservation $reservation, ReservationService $reservationService): JsonResponse
    {
        sleep(10);

        try {
            $reservationService->cancelReservation($reservation);
            return new JsonResponse([
                'message' => 'Réservation annulée'
            ], 200);
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("API : Erreur pendant l'annulation de la réservation", [
                    'exception' => $exception,
                    'reservation' => $reservation,
                ]);
            }

            return new JsonResponse([
                'message' => "Une erreur est survenue pendant l'annulation de la réservation."
            ], 500);
        }
    }

    /**
     * Endpoint de création d'un passager
     * @param CreatePassengerRequest $request
     * @return PassagerResource|JsonResponse
     */
    public function createPassager(CreatePassengerRequest $request): JsonResponse|PassagerResource
    {
        $validated = $request->validated();

        $user = User::find($validated['user_id']);
        $passenger = new Passager();

        try {
            $passenger->nom = $validated['nom'];
            $passenger->email = $validated['email'];
            $passenger->telephone = $validated['phone'];
            $passenger->portable = $validated['portable'];

            $user->passagers()->save($passenger);

            return new PassagerResource($passenger);
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()
                    ->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error("API : Erreur pendant la création du passager", [
                    'exception' => $exception,
                    'data' => $validated
                ]);
            }

            return new JsonResponse([
                'message' => "Erreur pendant la création du passager."
            ], 500);
        }
    }

    /**
     * Endpoint de création d'une nouvelle adresse de réservation
     * @param AddressReservationRequest $request
     * @return AdressesReservationResource|JsonResponse
     */
    public function createAddressReservation(AddressReservationRequest $request)
    {
        $validated = $request->validated();
        $address = new AdresseReservation();

        try {
            $address->adresse = $validated['adresse'];
            $address->adresse_complement = $validated['adresse_complement'];
            $address->ville = $validated['ville'];
            $address->code_postal = $validated['code_postal'];
            $address->user_id = $validated['user_id'];

            $address->save();

            return new AdressesReservationResource(
                $address
            );
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray()->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('API : Erreur pendant la création de l\'adresse de réservation', [
                    'exception' => $exception,
                    'data' => $validated
                ]);
            }

            return new JsonResponse([
                'message' => 'Erreur pendant la création de l\'adresse.'
            ], 500);
        }
    }

    /**
     * Endpoint de création d'une réservation
     */
    public function createReservation(ReservationRequest $request)
    {
        $validated = $request->validated();

        $reservation = new Reservation();
        $passenger = Passager::find($validated['passager_id']);
        $entreprise = Entreprise::find($validated['entreprise_id']);

        try {
            $reservation->pickup_date = Carbon::make($validated['pickup_date']);
            $reservation->passager()->associate($passenger);
            $reservation->entreprise()->associate($entreprise);

            // Gestion du lieu de prise en charge
            $reservation = $this->handleFromReservation(
                $reservation,
                $validated['location_from_id'],
                $validated['address_from_id']
            );

            // Gestion du lieu de destination
            $reservation = $this->handleToReservation(
                $reservation,
                $validated['location_to_id'],
                $validated['address_to_id']
            );

            if (!empty($validated['steps'])) {
                $reservation->has_steps = true;
                $reservation->steps = $validated['steps'];
            }

            $reservation->comment = $validated['comment'];

            $reservation->save();
        } catch (Exception $exception) {
            if (\App::environment(['local'])) {
                ray($validated)->exception($exception);
            }

            if (App::environment(['beta', 'prod'])) {
                Log::channel('sentry')->error('API : Erreur pendant la création de la réservation', [
                    'exception' => $exception,
                    'data' => $validated
                ]);
            }

            return new JsonResponse([
                'message' => 'Erreur pendant la création de la réservation'
            ], 500);
        }

        return new JsonResponse([
            'message' => 'Réservation correctement créée.'
        ], 201);
    }

    private function handleFromReservation(Reservation $reservation, ?int $idLocationFrom, ?int $idAddressFrom): Reservation
    {
        if ($idLocationFrom != null) {
            $location = Localisation::find($idLocationFrom);
            $reservation->localisationFrom()->associate($location);
            $reservation->adresseReservationFrom()->disassociate();
        }

        if ($idAddressFrom != null) {
            $address = AdresseReservation::find($idAddressFrom);
            $reservation->adresseReservationFrom()->associate($address);
            $reservation->localisationFrom()->disassociate();
        }

        return $reservation;
    }

    private function handleToReservation(Reservation $reservation, ?int $idLocationTo, ?int $idAddressTo): Reservation
    {
        if ($idLocationTo != null) {
            $location = Localisation::find($idLocationTo);
            $reservation->localisationTo()->associate($location);
            $reservation->adresseReservationTo()->disassociate();
        }

        if ($idAddressTo != null) {
            $address = AdresseReservation::find($idAddressTo);
            $reservation->adresseReservationTo()->associate($address);
            $reservation->localisationTo()->disassociate();
        }

        return $reservation;
    }
}
