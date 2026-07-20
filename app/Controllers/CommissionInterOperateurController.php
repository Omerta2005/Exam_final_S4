<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OperationModel;
use App\Models\CommissionInterOperateurModel;

class CommissionInterOperateurController extends BaseController
{
    private const OPERATEUR_YAS_ID = 2;

    public function index()
    {
        return $this->montants();
    }

    public function config()
    {
        $commissionModel = new CommissionInterOperateurModel();

        $operateur = [
            'id_operateur' => self::OPERATEUR_YAS_ID,
            'nom' => 'Yas',
            'pourcentage' => $commissionModel->getPourcentage(self::OPERATEUR_YAS_ID),
        ];

        return view('operateur/commissions/config', [
            'operateur' => $operateur,
        ]);
    }

    public function save()
    {
        $model = new CommissionInterOperateurModel();
        $idOperateur = self::OPERATEUR_YAS_ID;
        $pourcentageAffiche = $this->request->getPost('pourcentage'); // ex: 2 (pour 2%)
        $pourcentageStocke = $pourcentageAffiche / 100; // ex: 0.02

        $existant = $model->where('id_operateur', $idOperateur)->first();

        if ($existant) {
            $model->update($existant['id_commission'], ['pourcentage' => $pourcentageStocke]);
        } else {
            $model->insert(['id_operateur' => $idOperateur, 'pourcentage' => $pourcentageStocke]);
        }

        return redirect()->to('/operateur/commissions')->with('success', 'Commission mise à jour.');
    }

    public function montants()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $model = new OperationModel();
        $lignes = array_values(array_filter(
            $model->getMontantsAEnvoyer($dateDebut, $dateFin),
            static fn (array $ligne): bool => ($ligne['nom_operateur_source'] ?? null) === 'Yas'
        ));

        $totalGeneral = array_sum(array_map(static fn (array $ligne): float => (float) $ligne['montant_total'], $lignes));
        $commissionModel = new CommissionInterOperateurModel();
        $operateur = [
            'id_operateur' => self::OPERATEUR_YAS_ID,
            'nom' => 'Yas',
            'pourcentage' => $commissionModel->getPourcentage(self::OPERATEUR_YAS_ID),
        ];

        return view('operateur/commissions/montants', [
            'lignes' => $lignes,
            'totalGeneral' => $totalGeneral,
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            'nomOperateur' => 'Yas',
            'operateur' => $operateur,
        ]);
    }
}
