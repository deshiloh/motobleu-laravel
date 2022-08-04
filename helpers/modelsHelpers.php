<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AdresseEntreprise
 *
 * @property int $id
 * @property string $adresse
 * @property string|null $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property \App\Enum\AdresseEntrepriseTypeEnum $type
 * @property string $email
 * @property string $nom
 * @property string $tva
 * @property int|null $entreprise_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Entreprise|null $entreprise
 * @method static \Database\Factories\AdresseEntrepriseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereTva($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseEntreprise whereVille($value)
 */
	class IdeHelperAdresseEntreprise {}
}

namespace App\Models{
/**
 * App\Models\AdresseReservation
 *
 * @property int $id
 * @property string $adresse
 * @property string|null $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_adresse
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\AdresseReservationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdresseReservation whereVille($value)
 */
	class IdeHelperAdresseReservation {}
}

namespace App\Models{
/**
 * App\Models\CostCenter
 *
 * @property int $id
 * @property string $nom
 * @property int $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CostCenterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter query()
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CostCenter whereUpdatedAt($value)
 */
	class IdeHelperCostCenter {}
}

namespace App\Models{
/**
 * App\Models\Entreprise
 *
 * @property int $id
 * @property string $nom
 * @property int $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdresseEntreprise[] $adresseEntreprises
 * @property-read int|null $adresse_entreprises_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TypeFacturation[] $typeFacturations
 * @property-read int|null $type_facturations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\EntrepriseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Entreprise whereUpdatedAt($value)
 */
	class IdeHelperEntreprise {}
}

namespace App\Models{
/**
 * App\Models\Facture
 *
 * @property int $id
 * @property string $reference
 * @property float $montant_ht
 * @property int $tva
 * @property string|null $adresse_client
 * @property string|null $adresse_facturation
 * @property string|null $information
 * @property int|null $month
 * @property int|null $year
 * @property int $is_acquitte
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @method static \Database\Factories\FactureFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture query()
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereAdresseClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereAdresseFacturation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereIsAcquitte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereMontantHt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereTva($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Facture whereYear($value)
 */
	class IdeHelperFacture {}
}

namespace App\Models{
/**
 * App\Models\Localisation
 *
 * @property int $id
 * @property string $nom
 * @property string $adresse
 * @property string $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property string $telephone
 * @property int $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_adresse
 * @method static \Database\Factories\LocalisationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Localisation whereVille($value)
 */
	class IdeHelperLocalisation {}
}

namespace App\Models{
/**
 * App\Models\Passager
 *
 * @property int $id
 * @property string $nom
 * @property string|null $portable
 * @property string $telephone
 * @property string $email
 * @property int $is_actif
 * @property int $user_id
 * @property int|null $cost_center_id
 * @property int|null $type_facturation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CostCenter|null $costCenter
 * @property-read \App\Models\TypeFacturation|null $facturation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PassagerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Passager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Passager query()
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereCostCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager wherePortable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereTypeFacturationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passager whereUserId($value)
 */
	class IdeHelperPassager {}
}

namespace App\Models{
/**
 * App\Models\Pilote
 *
 * @property int $id
 * @property string $nom
 * @property string $prenom
 * @property string $telephone
 * @property string $email
 * @property string|null $entreprise
 * @property string|null $adresse
 * @property string|null $adresse_complement
 * @property string|null $code_postal
 * @property string|null $ville
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_name
 * @method static \Database\Factories\PiloteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pilote whereVille($value)
 */
	class IdeHelperPilote {}
}

namespace App\Models{
/**
 * App\Models\Reservation
 *
 * @property int $id
 * @property string|null $commande
 * @property string $reference
 * @property string|null $pickup_origin
 * @property string|null $drop_off_origin
 * @property string|null $event_id
 * @property string|null $comment
 * @property float|null $tarif
 * @property float|null $majoration
 * @property float|null $encaisse
 * @property float|null $encompte
 * @property float|null $complement
 * @property string|null $comment_facture
 * @property string|null $comment_pilote
 * @property int $send_to_passager
 * @property int $send_to_user
 * @property bool $is_confirmed
 * @property bool $is_cancel
 * @property int $has_back
 * @property int $is_cancel_pay
 * @property int $calendar_passager_invitation
 * @property int $calendar_user_invitation
 * @property int $is_billed
 * @property \Illuminate\Support\Carbon $pickup_date
 * @property int|null $localisation_from_id
 * @property int|null $localisation_to_id
 * @property int|null $adresse_reservation_from_id
 * @property int|null $adresse_reservation_to_id
 * @property int|null $passager_id
 * @property int|null $pilote_id
 * @property int|null $reservation_id
 * @property int|null $facture_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AdresseReservation|null $adresseReservationFrom
 * @property-read \App\Models\AdresseReservation|null $adresseReservationTo
 * @property-read \App\Models\Facture|null $facture
 * @property-read string $display_from
 * @property-read mixed $display_to
 * @property-read \App\Models\Localisation|null $localisationFrom
 * @property-read \App\Models\Localisation|null $localisationTo
 * @property-read \App\Models\Passager|null $passager
 * @property-read \App\Models\Pilote|null $pilote
 * @property-read Reservation|null $reservationBack
 * @method static \Database\Factories\ReservationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation toConfirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereAdresseReservationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereAdresseReservationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCalendarPassagerInvitation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCalendarUserInvitation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCommande($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCommentFacture($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCommentPilote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereDropOffOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEncaisse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEncompte($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereFactureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereHasBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereIsBilled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereIsCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereIsCancelPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereIsConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereLocalisationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereLocalisationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereMajoration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePassagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePickupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePickupOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation wherePiloteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereReservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereSendToPassager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereSendToUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereTarif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservation whereUpdatedAt($value)
 */
	class IdeHelperReservation {}
}

namespace App\Models{
/**
 * App\Models\TypeFacturation
 *
 * @property int $id
 * @property string $nom
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Entreprise $entreprise
 * @method static \Database\Factories\TypeFacturationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TypeFacturation whereUpdatedAt($value)
 */
	class IdeHelperTypeFacturation {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property boolean $is_actif
 * @property int $id
 * @property string|null $nom
 * @property string|null $prenom
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $adresse_bis
 * @property string|null $code_postal
 * @property string|null $ville
 * @property int $is_admin_ardian
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $entreprise_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdresseReservation[] $adresseReservations
 * @property-read int|null $adresse_reservations_count
 * @property-read \App\Models\Entreprise|null $entreprise
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Passager[] $passagers
 * @property-read int|null $passagers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAdresseBis($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAdminArdian($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereVille($value)
 */
	class IdeHelperUser {}
}

