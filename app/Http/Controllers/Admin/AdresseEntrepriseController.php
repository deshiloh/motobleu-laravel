<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Http\RedirectResponse;

class AdresseEntrepriseController extends Controller
{
    /**
     * @param Entreprise $entreprise
     * @param AdresseEntreprise $adress
     * @return RedirectResponse
     */
    public function destroy(Entreprise $entreprise, AdresseEntreprise $adress)
    {
        $adress->delete();
        return redirect()
            ->route('admin.entreprises.show', ['entreprise' => $entreprise->id])
            ->with('success', "L'adresse a bien été supprimée.");
    }
}
