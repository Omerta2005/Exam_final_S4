<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremefrais';
    protected $primaryKey       = 'id_bareme';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id_type_operation',
        'montant_min',
        'montant_max',
        'valeur_frais'
    ];

    public function getAllWithTypeOperation()
    {
        return $this->select('baremefrais.id_bareme, baremefrais.montant_min, baremefrais.montant_max, baremefrais.valeur_frais, typeoperation.nom as nom_type_operation')
                    ->join('typeoperation', 'typeoperation.id_type_operation = baremefrais.id_type_operation')
                    ->findAll();
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
