<?php

namespace Database\Factories;

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
    public function definition()
    {
        return [
            'reference' => '202207' . self::$ref ++,
            'pickup_origin' => 'vol num 4440',
            'drop_off_origin' => 'vol num 4440',
            'comment' => 'lorem',
            'send_to_passager' => true,
            'send_to_user' => true,
            'is_confirmed' => false,
            'is_cancel' => false,
            'has_back' => false,
            'pickup_date' => $this->faker->dateTime,
            'localisation_from_id' => Localisation::factory(),
            'localisation_to_id' => Localisation::factory(),
        ];
    }
}
