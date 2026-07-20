<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'Prefixe';
    protected $primaryKey       = 'id_prefixe';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_operateur',
        'code',
        'actif'
    ];

    public function getAllWithOperateur()
    {
        return $this->select('Prefixe.id_prefixe, Prefixe.code, Prefixe.actif, Operateur.nom as nom_operateur')
                    ->join('Operateur', 'Operateur.id_operateur = Prefixe.id_operateur')
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
    // protected $validationRules = [
    //     'code' => [
    //         'exact_length[3]',
    //         'is_unique[prefixe.code]'
    //     ],
    //     'id_operateur' => [
    //         'required'
    //     ],
    // ];

    //  protected $validationMessages = [
    //     'code' => [
    //         'is_unique' => 'Ce code de préfixe existe déjà.',
    //     ],
    //     'id_operateur' => [
    //         'required' => 'Vous devez sélectionner un opérateur.',
    //     ],
    // ];

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
