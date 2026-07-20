<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BaremeFraisModel;

class BaremeFraisController extends BaseController
{
    public function index()
    {
        $BaremeFrais = new BaremeFraisModel();
        $baremeFrais = $BaremeFrais->findAll();
        return $this->response->setJSON($baremeFrais);
    }
}
