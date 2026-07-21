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

}
