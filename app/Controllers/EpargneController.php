<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EpargneModel;

class EpargneController extends BaseController
{
    public function index()
    {
        return view('client/epargne');
    }

    public function insert(){
        $pourcentage =  trim($this->request->getVar('epargne')); 
        $id_client = session()->get('id_client');
        $data =[
            'id_client'=> $id_client,
            'pourcentage'=> $pourcentage,
        ];
        $model = new EpargneModel();
        $success = $model->insert($data);
        return redirect()->to(base_url('client/epargne'));
    }

}
