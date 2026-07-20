<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OperationModel;

class GainController extends BaseController
{
    public function index()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $model = new OperationModel();
        $lignes = $model->getGainsParOperateurEtType($dateDebut, $dateFin);

        $groupes = [];
        foreach ($lignes as $ligne) {
            $nomOp = $ligne['nom_operateur'];

            if (! isset($groupes[$nomOp])) {
                $groupes[$nomOp] = ['lignes' => [], 'total' => 0];
            }

            $groupes[$nomOp]['lignes'][] = $ligne;
            $groupes[$nomOp]['total'] += $ligne['total_frais'];
        }

        $gainGlobal = array_sum(array_column($groupes, 'total'));

        return view('operateur/gains/index', [
            'groupes'    => $groupes,
            'gainGlobal' => $gainGlobal,
            'dateDebut'  => $dateDebut,   // <-- vérifie que cette ligne existe
            'dateFin'    => $dateFin,     // <-- et celle-ci
        ]);
    }
}
