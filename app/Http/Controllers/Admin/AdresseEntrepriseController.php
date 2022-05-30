<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdresseEntrepriseRequest;
use App\Models\AdresseEntreprise;
use App\Models\Entreprise;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use function PHPUnit\Framework\directoryExists;

class AdresseEntrepriseController extends Controller
{
    /**
     * Display Form update
     * @param Entreprise $entreprise
     * @param AdresseEntreprise $adress
     * @return Application|Factory|View
     */
    public function edit(Entreprise $entreprise, AdresseEntreprise $adress)
    {
        return view('admin.adresseEntreprise.form', [
            'entreprise' => $entreprise,
            'adresse' => $adress
        ]);
    }

    /**
     * Met à jour l'adresse entreprise
     * @return RedirectResponse
     */
    public function update(AdresseEntrepriseRequest $request, Entreprise $entreprise, AdresseEntreprise $adress)
    {
        $request->validated();

        $request->getSession()->set('entreprise', $entreprise);

        $adress->update((array) $request->input());
        $adress->save();

        return back()->with('success', "L'adresse a bien été mise à jour");
    }

    /**
     * Display create AdresseEntreprise form
     * @param Entreprise $entreprise
     * @return Application|Factory|View
     */
    public function create(Entreprise $entreprise)
    {
        return view('admin.adresseEntreprise.form', [
            'entreprise' => $entreprise
        ]);
    }

    /**
     * Save AdresseEntreprise in the database
     * @param AdresseEntrepriseRequest $request
     * @param Entreprise $entreprise
     * @return RedirectResponse
     */
    public function store(AdresseEntrepriseRequest $request, Entreprise $entreprise)
    {
        $request->validated();

        $entreprise->adresseEntreprises()->create($request->input());

        return redirect()
            ->route('admin.entreprises.show', ['entreprise' => $entreprise->id])
            ->with('success', "L'adresse a bien été ajoutée.");
    }

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
