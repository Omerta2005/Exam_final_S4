<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'BaremeFrais';
    protected $primaryKey       = 'id_bareme';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'id_type_operation',
        'id_operateur',
        'montant_min',
        'montant_max',
        'valeur_frais'
    ];

    public function getAllWithOperateurEtType()
    {
        return $this->select('BaremeFrais.id_bareme, BaremeFrais.montant_min, BaremeFrais.montant_max, BaremeFrais.valeur_frais,
                            TypeOperation.libelle as libelle_type_operation,
                            Operateur.id_operateur, Operateur.nom as nom_operateur')
                    ->join('TypeOperation', 'TypeOperation.id_type_operation = BaremeFrais.id_type_operation')
                    ->join('Operateur', 'Operateur.id_operateur = BaremeFrais.id_operateur')
                    ->orderBy('Operateur.nom')
                    ->orderBy('TypeOperation.libelle')
                    ->orderBy('BaremeFrais.montant_min')
                    ->findAll();
    }

    /**
     * Calcule le montant des frais applicables pour une opération donnée
     *
     * @param int   $idOperateur       ID de l'opérateur du client
     * @param int   $idTypeOperation   ID du type d'opération (retrait, transfert...)
     * @param float $montant           Montant de l'opération
     * @return float                   Montant des frais (0 si aucune tranche ne correspond)
     */
    public function calculerFrais(int $idOperateur, int $idTypeOperation, float $montant): float
    {
        $tranche = $this->where('id_operateur', $idOperateur)
                         ->where('id_type_operation', $idTypeOperation)
                         ->where('montant_min <=', $montant)
                         ->where('montant_max >=', $montant)
                         ->first();

        return $tranche ? (float) $tranche['valeur_frais'] : 0.0;
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
