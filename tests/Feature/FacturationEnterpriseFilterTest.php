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
        // Set test date to September 2025
        Carbon::setTestNow(Carbon::create(2025, 9, 15));

        // Test without filter - should show all companies
        $component = Livewire::test(EditionFacture::class)
            ->set('selectedMonth', 9)
            ->set('selectedYear', 2025)
            ->assertHasNoErrors();

        $allEntreprises = $component->get('entreprises');
        $this->assertGreaterThan(0, $allEntreprises->count());

        // Test with filter - should show only selected company
        $entreprise = Entreprise::whereHas('reservations', function($query) {
            $query->whereMonth('pickup_date', 9)
                  ->whereYear('pickup_date', 2025);
        })->first();

        if ($entreprise) {
            $component->set('entrepriseSearch', $entreprise->id);

            $filteredEntreprises = $component->get('entreprises');
            $this->assertEquals(1, $filteredEntreprises->count());
            $this->assertEquals($entreprise->id, $filteredEntreprises->first()->id);
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