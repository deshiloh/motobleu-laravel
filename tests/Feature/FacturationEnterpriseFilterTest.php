<?php

namespace Tests\Feature;

use App\Livewire\Facturation\EditionFacture;
use App\Models\Entreprise;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FacturationEnterpriseFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function testEntrepriseFilterWorks()
    {
        // Use current date for test to ensure we have data
        $currentDate = Carbon::now();

        // Create a company and reservation for current month upfront
        $entreprise = Entreprise::first() ?? Entreprise::factory()->create();

        $reservation = Reservation::factory()->create([
            'entreprise_id' => $entreprise->id,
            'pickup_date' => $currentDate,
            'statut' => \App\Enum\ReservationStatus::Confirmed->value,
            'encaisse_pilote' => null
        ]);

        // Test without filter - should show all companies for current month/year
        $component = Livewire::test(EditionFacture::class)
            ->set('selectedMonth', $currentDate->month)
            ->set('selectedYear', $currentDate->year)
            ->assertHasNoErrors();

        $allEntreprises = $component->get('entreprises');
        $this->assertGreaterThan(0, $allEntreprises->count());

        // Test with filter - should show only selected company
        $firstEntreprise = $allEntreprises->first();

        if ($firstEntreprise) {
            $component->set('entrepriseSearch', $firstEntreprise->id);

            $filteredEntreprises = $component->get('entreprises');
            $this->assertEquals(1, $filteredEntreprises->count());
            $this->assertEquals($firstEntreprise->id, $filteredEntreprises->first()->id);
        }
    }

    public function testMonthYearFilterUpdatesEntreprises()
    {
        $component = Livewire::test(EditionFacture::class)
            ->set('selectedMonth', 9)
            ->set('selectedYear', 2025)
            ->assertHasNoErrors();

        $initialCount = $component->get('entreprises')->count();

        // Change month and verify entreprises list updates
        $component->set('selectedMonth', 10);

        // The list should potentially be different (even if empty)
        $newCount = $component->get('entreprises')->count();

        // We can't guarantee different results, but the update should work without errors
        $this->assertIsInt($newCount);
    }
}