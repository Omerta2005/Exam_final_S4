<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table            = 'Operation';
    protected $primaryKey       = 'id_operation';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_type_operation',
        'id_compte_source',
        'id_compte_destination',
        'montant',
        'frais_appliques',
        'date_operation',
        'id_statut'
    ];

    public function getGainsParType(?string $dateDebut = null, ?string $dateFin = null)
    {
        $builder = $this->select('TypeOperation.libelle as type_operation,
                                   SUM(Operation.frais_appliques) as total_frais,
                                   COUNT(Operation.id_operation) as nombre_operations')
                         ->join('TypeOperation', 'TypeOperation.id_type_operation = Operation.id_type_operation')
                         ->join('statut_operation', 'statut_operation.id_statut = Operation.id_statut')
                         ->where('statut_operation.libelle', 'reussie')
                         ->where('TypeOperation.libelle !=', 'depot'); // pas de frais sur les dépôts

        if ($dateDebut) {
            $builder->where('Operation.date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('Operation.date_operation <=', $dateFin);
        }

        return $builder->groupBy('TypeOperation.libelle')->findAll();
    }

    public function getGainTotal(?string $dateDebut = null, ?string $dateFin = null)
    {
        $builder = $this->select('SUM(frais_appliques) as total')
                         ->join('statut_operation', 'statut_operation.id_statut = Operation.id_statut')
                         ->where('statut_operation.libelle', 'reussie');

        if ($dateDebut) {
            $builder->where('date_operation >=', $dateDebut);
        }
        if ($dateFin) {
            $builder->where('date_operation <=', $dateFin);
        }

        $result = $builder->first();
        return $result['total'] ?? 0;
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
