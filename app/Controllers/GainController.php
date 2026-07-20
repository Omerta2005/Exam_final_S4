<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OperationModel;

class GainController extends BaseController
{
    private const OPERATEUR_YAS_ID = 2;

    public function index()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $model = new OperationModel();
        $lignes = $model->getGainsParOperateurEtType($dateDebut, $dateFin, self::OPERATEUR_YAS_ID);

        $groupes = [
            'retrait' => [
                'label' => 'Retraits',
                'lignes' => [],
                'total' => 0,
            ],
            'meme_operateur' => [
                'label' => 'Transferts vers Yas',
                'lignes' => [],
                'total' => 0,
            ],
            'autre_operateur' => [
                'label' => 'Transferts vers autres opérateurs',
                'lignes' => [],
                'total' => 0,
            ],
        ];

        foreach ($lignes as $ligne) {
            if ($ligne['type_operation'] === 'retrait') {
                $key = 'retrait';
            } elseif ($ligne['portee'] === 'meme_operateur') {
                $key = 'meme_operateur';
            } elseif ($ligne['portee'] === 'autre_operateur') {
                $key = 'autre_operateur';
            } else {
                continue;
            }

            $groupes[$key]['lignes'][] = $ligne;
            $groupes[$key]['total'] += (float) $ligne['total_frais'];
        }

        $gainGlobal = array_sum(array_column($groupes, 'total'));

        return view('operateur/gains/index', [
            'groupes'    => $groupes,
            'gainGlobal' => $gainGlobal,
            'dateDebut'  => $dateDebut,   // <-- vérifie que cette ligne existe
            'dateFin'    => $dateFin,     // <-- et celle-ci
            'nomOperateur' => 'Yas',
        ]);
    }
}
