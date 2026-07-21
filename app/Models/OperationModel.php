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
        'id_operateur_destination',
        'montant',
        'frais_appliques',
        'id_statut',
    ];

    protected bool $allowEmptyInserts = false;
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
     * Historique des operations d'un client : toutes les operations ou son compte
     * est source (retrait, transfert envoye) ou destination (depot, transfert recu).
     */
    public function getHistoriqueByClient(int $idClient, int $limite = 50): array
    {
        $db = db_connect();

        $idsComptes = $db->table('Compte')
            ->select('id_compte')
            ->where('id_client', $idClient)
            ->get()
            ->getResultArray();

        $idsComptes = array_column($idsComptes, 'id_compte');

        if (empty($idsComptes)) {
            return [];
        }

        $resultats = $db->table('vue_historique_operations')
            ->groupStart()
                ->whereIn('id_compte_source', $idsComptes)
                ->orWhereIn('id_compte_destination', $idsComptes)
            ->groupEnd()
            ->orderBy('date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();

        foreach ($resultats as &$op) {
            $op['est_source'] = in_array($op['id_compte_source'], $idsComptes);
        }

        return $resultats;
    }

    /**
     * Historique de toutes les operations touchant un compte precis (source OU
     * destination), utilise cote operateur pour la fiche detail d'un compte.
     */
    public function getHistoriqueParCompte(int $idCompte, int $limite = 100): array
    {
        return db_connect()
            ->table('vue_historique_operations')
            ->groupStart()
                ->where('id_compte_source', $idCompte)
                ->orWhere('id_compte_destination', $idCompte)
            ->groupEnd()
            ->orderBy('date_operation', 'DESC')
            ->limit($limite)
            ->get()
            ->getResultArray();
    }

    /**
     * Montants a reverser entre operateurs : regroupe, par operateur source et
     * operateur destination, le total des transferts REUSSIS et INTER-OPERATEURS
     * sur une periode donnee. Utilise pour la page "Commissions inter-operateur".
     *
     * Chaque ligne retournee contient : nom_operateur_source, nom_operateur_destination,
     * nombre_operations, montant_total (somme des montants transferes, hors frais),
     * frais_total (somme des frais_appliques, correspondant a la commission percue).
     */
    public function getMontantsAEnvoyer(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = db_connect()
            ->table('vue_commissions_inter_operateurs');

        if ($dateDebut) {
            $builder->where('date_operation >=', $dateDebut . ' 00:00:00');
        }

        if ($dateFin) {
            $builder->where('date_operation <=', $dateFin . ' 23:59:59');
        }

        return $builder
            ->select('
                nom_operateur_source,
                nom_operateur_dest,
                COUNT(id_operation) AS nombre_transferts,
                SUM(montant) AS montant_total,
                SUM(frais_appliques) AS frais_total
            ')
            ->groupBy('nom_operateur_source, nom_operateur_dest')
            ->get()
            ->getResultArray();
    }

    public function getOperationsPourGains(?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = db_connect()->table('vue_operations_gains');

        if ($dateDebut) {
            $builder->where('date_operation >=', $dateDebut . ' 00:00:00');
        }

        if ($dateFin) {
            $builder->where('date_operation <=', $dateFin . ' 23:59:59');
        }

        return $builder
            ->orderBy('date_operation', 'DESC')
            ->orderBy('id_operation', 'DESC')
            ->get()
            ->getResultArray();
    }
}