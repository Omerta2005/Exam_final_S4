<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table            = 'Operation';
    protected $primaryKey       = 'id_operation';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_type_operation',
        'id_compte_source',
        'id_compte_destination',
        'montant',
        'frais_appliques',
        'id_statut',
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

    // Dates : la colonne date_operation a deja un DEFAULT en base (SQLite),
    // on laisse le SGBD la remplir plutot que de gerer un timestamp CI ici.
    protected $useTimestamps = false;

    // Constantes correspondant aux lignes inserees dans TypeOperation
    public const TYPE_DEPOT     = 1;
    public const TYPE_RETRAIT   = 2;
    public const TYPE_TRANSFERT = 3;

    // Constantes correspondant aux lignes inserees dans statut_operation
    public const STATUT_EN_ATTENTE = 1;
    public const STATUT_REUSSIE    = 2;
    public const STATUT_ANNULEE    = 3;

    /**
     * Calcule les frais a appliquer pour un type d'operation et un montant donnes,
     * en se basant sur la grille BaremeFrais. Retourne 0 si aucune tranche ne correspond
     * (c'est le cas du depot, qui n'a pas de bareme).
     */
    public function getFrais(int $id_type_operation, float $montant): float
    {
        $db = \Config\Database::connect();

        $bareme = $db->table('BaremeFrais')
            ->where('id_type_operation', $id_type_operation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->get()
            ->getRowArray();

        return $bareme ? (float) $bareme['valeur_frais'] : 0.0;
    }

    /**
     * Historique des operations d'un client : toutes les operations ou son compte
     * est source (retrait, transfert envoye) ou destination (depot, transfert recu).
     */
    public function getHistoriqueByClient(int $id_client, int $limite = 50): array
    {
        $db = \Config\Database::connect();

        // On recupere d'abord le(s) id_compte du client (normalement un seul)
        $comptes = $db->table('Compte')->where('id_client', $id_client)->get()->getResultArray();
        $idsComptes = array_column($comptes, 'id_compte');

        if (empty($idsComptes)) {
            return [];
        }

        $builder = $db->table('Operation')
            ->select('
                Operation.id_operation,
                Operation.montant,
                Operation.frais_appliques,
                Operation.date_operation,
                Operation.id_compte_source,
                Operation.id_compte_destination,
                TypeOperation.libelle AS type_libelle,
                statut_operation.libelle AS statut_libelle,
                ClientSource.numero_telephone AS numero_source,
                ClientSource.nom AS nom_source,
                ClientDestination.numero_telephone AS numero_destination,
                ClientDestination.nom AS nom_destination
            ')
            ->join('TypeOperation', 'TypeOperation.id_type_operation = Operation.id_type_operation')
            ->join('statut_operation', 'statut_operation.id_statut = Operation.id_statut')
            ->join('Compte AS CompteSource', 'CompteSource.id_compte = Operation.id_compte_source', 'left')
            ->join('Client AS ClientSource', 'ClientSource.id_client = CompteSource.id_client', 'left')
            ->join('Compte AS CompteDestination', 'CompteDestination.id_compte = Operation.id_compte_destination', 'left')
            ->join('Client AS ClientDestination', 'ClientDestination.id_client = CompteDestination.id_client', 'left')
            ->groupStart()
                ->whereIn('Operation.id_compte_source', $idsComptes)
                ->orWhereIn('Operation.id_compte_destination', $idsComptes)
            ->groupEnd()
            ->orderBy('Operation.date_operation', 'DESC')
            ->limit($limite);

        $resultats = $builder->get()->getResultArray();

        // On annote chaque ligne avec le sens (debit/credit) du point de vue de CE client
        foreach ($resultats as &$op) {
            $op['est_source'] = in_array($op['id_compte_source'], $idsComptes, true);
        }

        return $resultats;
    }
}