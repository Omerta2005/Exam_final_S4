<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CommissionInterOperateurModel;
use App\Models\OperateurModel;

class CommissionInterOperateurController extends BaseController
{
    public function index()
    {
        $operateurModel = new OperateurModel();
        $commissionModel = new CommissionInterOperateurModel();

        $operateurs = $operateurModel->findAll();
        foreach ($operateurs as &$op) {
            $op['pourcentage'] = $commissionModel->getPourcentage($op['id_operateur']);
        }

        return view('operateur/commissions/index', ['operateurs' => $operateurs]);
    }

    public function save()
    {
        $model = new CommissionInterOperateurModel();
        $idOperateur = $this->request->getPost('id_operateur');
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
}
