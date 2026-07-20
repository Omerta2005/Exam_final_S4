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

        $gainsParType = $model->getGainsParType($dateDebut, $dateFin);
        $gainTotal    = $model->getGainTotal($dateDebut, $dateFin);

        return view('operateur/gains/index', [
            'gainsParType' => $gainsParType,
            'gainTotal'    => $gainTotal,
            'dateDebut'    => $dateDebut,
            'dateFin'      => $dateFin,
        ]);
    }
}
