<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PrefixeModel;
use App\Models\OperateurModel;
use CodeIgniter\HTTP\ResponseInterface;

class PrefixeController extends BaseController
{
    public function index()
    {
        $model = new PrefixeModel();
        $prefixes = $model->getAllWithOperateur();

        return view('operateur/prefixe/index', [
            'prefixes' => $prefixes
        ]);
    }

    public function form()
    {
        $id = $this->request->getGet('id');

        $prefixeModel  = new PrefixeModel();
        $operateurModel = new OperateurModel();

        $prefixe = $id ? $prefixeModel->find($id) : null;

        return view('operateur/prefixe/form', [
            'prefixe'   => $prefixe,
            'operateurs' => $operateurModel->findAll()
        ]);
    }

    public function save()
    {
        $model = new PrefixeModel();

        $data = [
            'id_operateur' => $this->request->getPost('id_operateur'),
            'code' => $this->request->getPost('code'),
            'actif' => $this->request->getPost('actif') ? 1 : 0,
        ];

        $id = $this->request->getPost('id_prefixe');

        if ($id) {
            $succes = $model->update($id, $data);   // modification
        } else {
            $succes = $model->insert($data);        // création
        }

        // if (! $succes) {
        //     return redirect()->back()->withInput()->with('errors', $model->errors());
        // }

        return redirect()->to('/operateur/prefixe');
    }
}
