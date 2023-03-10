<?php

namespace Database\Factories;

use App\Enum\ReservationStatus;
use App\Models\Facture;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ReservationFactory extends Factory
{

    static int $ref = 1;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commande' => '345678HFG',
            'reference' => '202207' . self::$ref ++,
            'statut' => ReservationStatus::Created,
            'pickup_origin' => 'vol num 4440',
            'drop_off_origin' => 'vol num 4440',
            'comment' => 'lorem',
            'send_to_passager' => true,
            'calendar_passager_invitation' => true,
            'has_back' => false,
            'pickup_date' => $this->faker->dateTime,
            'localisation_from_id' => Localisation::factory(),
            'localisation_to_id' => Localisation::factory()
        ];
    }
}
