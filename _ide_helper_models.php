<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @mixin IdeHelperAdresseEntreprise
 * @property int $id
 * @property string $adresse
 * @property string|null $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property \App\Enum\AdresseEntrepriseTypeEnum $type
 * @property string $email
 * @property string $nom
 * @property string|null $tva
 * @property int|null $entreprise_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $address_bill_format
 * @property-read mixed $adresse_full
 * @property-read \App\Models\Entreprise|null $entreprise
 * @property-read mixed $type_name
 * @method static \Database\Factories\AdresseEntrepriseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereTva($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseEntreprise whereVille($value)
 */
	class AdresseEntreprise extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperAdresseReservation
 * @property int $id
 * @property string $adresse
 * @property string|null $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property bool $is_actif
 * @property int $is_deleted
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_adresse
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\AdresseReservationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AdresseReservation whereVille($value)
 */
	class AdresseReservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperCarousel
 * @property int $id
 * @property string|null $file_name
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CarouselFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carousel whereUpdatedAt($value)
 */
	class Carousel extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperCostCenter
 * @property int $id
 * @property string $nom
 * @property bool $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\CostCenterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CostCenter whereUpdatedAt($value)
 */
	class CostCenter extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperEntreprise
 * @property int $id
 * @property string $nom
 * @property string|null $responsable_name
 * @property bool $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdresseEntreprise> $adresseEntreprises
 * @property-read int|null $adresse_entreprises_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TypeFacturation> $typeFacturations
 * @property-read int|null $type_facturations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\EntrepriseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereResponsableName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entreprise whereUpdatedAt($value)
 */
	class Entreprise extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperFacture
 * @property int $id
 * @property \App\Enum\BillStatut $statut
 * @property string|null $reference
 * @property float $montant_ttc
 * @property float $montant_tva
 * @property int $tva
 * @property string|null $adresse_client
 * @property string|null $adresse_facturation
 * @property string|null $information
 * @property int|null $month
 * @property int|null $year
 * @property bool $is_acquitte
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $billed_at
 * @property-read mixed $address_bill_inline
 * @property-read mixed $address_client_inline
 * @property-read mixed $montant_ht
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @method static \Database\Factories\FactureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereAdresseClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereAdresseFacturation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereBilledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereIsAcquitte($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereMontantTtc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereMontantTva($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereTva($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facture whereYear($value)
 */
	class Facture extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperLocalisation
 * @property int $id
 * @property string $nom
 * @property string $adresse
 * @property string $adresse_complement
 * @property string $code_postal
 * @property string $ville
 * @property string $telephone
 * @property bool $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_adresse
 * @method static \Database\Factories\LocalisationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Localisation whereVille($value)
 */
	class Localisation extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPage
 * @property int $id
 * @property array<array-key, mixed>|null $title
 * @property array<array-key, mixed> $content
 * @property array<array-key, mixed>|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPassager
 * @property int $id
 * @property string $nom
 * @property string|null $portable
 * @property string|null $telephone
 * @property string $email
 * @property bool $is_actif
 * @property int $user_id
 * @property int|null $cost_center_id
 * @property int|null $type_facturation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CostCenter|null $costCenter
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \App\Models\TypeFacturation|null $typeFacturation
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PassagerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereCostCenterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager wherePortable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereTypeFacturationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Passager whereUserId($value)
 */
	class Passager extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperPilote
 * @property int $id
 * @property string $nom
 * @property string $prenom
 * @property string|null $telephone
 * @property string $email
 * @property string|null $entreprise
 * @property string|null $adresse
 * @property string|null $adresse_complement
 * @property string|null $code_postal
 * @property string|null $ville
 * @property bool $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $commission
 * @property-read mixed $full_adresse
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @method static \Database\Factories\PiloteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereAdresseComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereEntreprise($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pilote whereVille($value)
 */
	class Pilote extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperReservation
 * @property int $id
 * @property \App\Enum\ReservationStatus $statut
 * @property string|null $commande
 * @property string|null $reference
 * @property string|null $pickup_origin
 * @property string|null $drop_off_origin
 * @property string|null $event_id
 * @property string|null $event_secretary_id
 * @property float|null $tarif
 * @property float|null $majoration
 * @property float|null $complement
 * @property float|null $encompte_pilote
 * @property float|null $encaisse_pilote
 * @property string|null $comment_facture
 * @property string|null $comment_pilote
 * @property string|null $comment
 * @property bool $send_to_passager
 * @property bool $calendar_passager_invitation
 * @property bool $has_back
 * @property \Illuminate\Support\Carbon $pickup_date
 * @property int|null $localisation_from_id
 * @property int|null $localisation_to_id
 * @property int|null $adresse_reservation_from_id
 * @property int|null $adresse_reservation_to_id
 * @property int|null $passager_id
 * @property int|null $pilote_id
 * @property int|null $reservation_id
 * @property int|null $facture_id
 * @property int|null $entreprise_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $has_steps
 * @property string|null $steps
 * @property string|null $commission
 * @property-read \App\Models\AdresseReservation|null $adresseReservationFrom
 * @property-read \App\Models\AdresseReservation|null $adresseReservationTo
 * @property-read \App\Models\Entreprise|null $entreprise
 * @property-read \App\Models\Facture|null $facture
 * @property-read string|null $display_from
 * @property-read string|null $display_to
 * @property-read int|float $total_ttc
 * @property-read \App\Models\Localisation|null $localisationFrom
 * @property-read \App\Models\Localisation|null $localisationTo
 * @property-read \App\Models\Passager|null $passager
 * @property-read \App\Models\Pilote|null $pilote
 * @property-read Reservation|null $reservationBack
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation toConfirmed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereAdresseReservationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereAdresseReservationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCalendarPassagerInvitation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCommande($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCommentFacture($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCommentPilote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereComplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereDropOffOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEncaissePilote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEncomptePilote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereEventSecretaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereFactureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereHasBack($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereHasSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereLocalisationFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereLocalisationToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereMajoration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePassagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePickupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePickupOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation wherePiloteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereReservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereSendToPassager($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereTarif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUpdatedAt($value)
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @mixin IdeHelperTypeFacturation
 * @property int $id
 * @property string $nom
 * @property bool $is_actif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\TypeFacturationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TypeFacturation whereUpdatedAt($value)
 */
	class TypeFacturation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property boolean $is_actif
 * @mixin IdeHelperUser
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
 * @property bool $is_admin
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AdresseReservation> $adresseReservations
 * @property-read int|null $adresse_reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entreprise> $entreprises
 * @property-read int|null $entreprises_count
 * @property-read mixed $full_name
 * @property-read mixed $is_admin_ardian
 * @property-read mixed $is_admin_role
 * @property-read mixed $is_ardian
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Passager> $passagers
 * @property-read int|null $passagers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdresse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAdresseBis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCodePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVille($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

