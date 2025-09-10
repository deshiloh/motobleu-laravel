<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Http\RedirectResponse;

class AdresseEntrepriseController extends Controller
{
    /**
     * @param int $entreprise
     * @param int $adress
     * @return RedirectResponse
     */
    public function destroy($entreprise, $adress)
    {
        $adresseEntreprise = AdresseEntreprise::findOrFail($adress);
        $adresseEntreprise->delete();
        return redirect()
            ->route('admin.entreprises.show', ['entreprise' => $entreprise])
            ->with('success', "L'adresse a bien été supprimée.");
    }
}
