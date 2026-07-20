<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    public const OPERATEUR_YAS_ID = 2;

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

    public function getAllWithOperateurEtType(?int $idOperateur = null)
    {
        $builder = $this->select('BaremeFrais.id_bareme, BaremeFrais.montant_min, BaremeFrais.montant_max, BaremeFrais.valeur_frais,
                            TypeOperation.libelle as libelle_type_operation,
                            Operateur.id_operateur, Operateur.nom as nom_operateur')
                    ->join('TypeOperation', 'TypeOperation.id_type_operation = BaremeFrais.id_type_operation')
                    ->join('Operateur', 'Operateur.id_operateur = BaremeFrais.id_operateur')
                    ->orderBy('Operateur.nom')
                    ->orderBy('TypeOperation.libelle')
                    ->orderBy('BaremeFrais.montant_min');

        if ($idOperateur !== null) {
            $builder->where('BaremeFrais.id_operateur', $idOperateur);
        }

        return $builder->findAll();
    }

    public function calculerFrais(int $idOperateurSource, int $idTypeOperation, float $montant, ?int $idOperateurDestination = null): float
    {
        $fraisBase = $this->calculerFraisBase($idOperateurSource, $idTypeOperation, $montant);

        $estTransfertInterOperateur = $idOperateurDestination !== null
                                    && $idOperateurDestination !== $idOperateurSource;

        if ($estTransfertInterOperateur) {
            $commissionModel = new \App\Models\CommissionInterOperateurModel();
            $pourcentage = $commissionModel->getPourcentage($idOperateurDestination);
            $fraisBase += $montant * $pourcentage;
        }

        return $fraisBase;
    }

    public function calculerFraisBase(int $idOperateur, int $idTypeOperation, float $montant): float
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
