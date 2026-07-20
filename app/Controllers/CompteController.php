<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompteModel;
use App\Models\OperationModel;

class CompteController extends BaseController
{
    public function index()
    {
        $recherche = $this->request->getGet('recherche');

        $model = new CompteModel();

        return view('operateur/comptes/index', [
            'comptes'   => $model->getAllWithClient($recherche),
            'recherche' => $recherche,
        ]);
    }

    public function show($idCompte)
    {
        $compteModel = new CompteModel();
        $operationModel = new OperationModel();
        $compte = $compteModel->getDetailCompte($idCompte);

        if (! $compte) {
            return redirect()->to('/operateur/comptes')->with('errors', ['Compte introuvable.']);
        }

        return view('operateur/comptes/show', [
            'compte'     => $compte,
            'historique' => $operationModel->getHistoriqueParCompte($idCompte),
        ]);
    }
}
