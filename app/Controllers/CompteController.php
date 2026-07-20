<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CompteController extends BaseController
{
    public function index()
    {
        //
    }
    public function create($id_client)
    {
        $db = \Config\Database::connect();

        $compte = $db->table('Compte')
            ->where('id_client', $id_client)
            ->get()
            ->getRowArray();

        if ($compte) {
            return $compte;
        }

        $data = [
            'id_client' => $id_client,
            'solde' => 0
        ];

        $db->table('Compte')->insert($data);

        return [
            'id_compte' => $db->insertID(),
            'id_client' => $id_client,
            'solde' => 0
        ];
    }
}
