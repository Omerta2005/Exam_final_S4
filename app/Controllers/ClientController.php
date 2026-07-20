<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientModel;

class ClientController extends BaseController
{
    public function index()
    {
        return view ('client/login');
    }
    public function login()
    {
        $numero_telephone = $this->request->getVar('numero_telephone');
        $clientModel = new ClientModel();
        $client = $clientModel->where('numero_telephone', $numero_telephone)->first();

        if ($client) {
            return $this->response->setJSON($client);
        } else {
            return $this->response->setStatusCode(ResponseInterface::HTTP_NOT_FOUND)
                ->setJSON(['message' => 'Client not found']);
        }
    }
}
