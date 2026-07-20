<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table            = 'Compte';
    protected $primaryKey       = 'id_compte';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_client',
        'solde',
        'date_creation'
    ];

    public function getAllWithClient(?string $recherche = null)
    {
        $builder = $this->select('Compte.id_compte, Compte.solde,
                                   Client.id_client, Client.numero_telephone, Client.nom,
                                   Operateur.nom as nom_operateur')
                         ->join('Client', 'Client.id_client = Compte.id_client')
                         ->join('Operateur', 'Operateur.id_operateur = Client.id_operateur');

        if ($recherche) {
            $builder->like('Client.numero_telephone', $recherche);
        }

        return $builder->orderBy('Client.numero_telephone')->findAll();
    }

    public function getDetailCompte(int $idCompte)
    {
        return $this->select('Compte.id_compte, Compte.solde,
                               Client.id_client, Client.numero_telephone, Client.nom,
                               Operateur.nom as nom_operateur')
                     ->join('Client', 'Client.id_client = Compte.id_client')
                     ->join('Operateur', 'Operateur.id_operateur = Client.id_operateur')
                     ->where('Compte.id_compte', $idCompte)
                     ->first();
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
