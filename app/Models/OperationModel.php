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
    public function getHistoriqueByClient(int $id_client, int $limite = 50): array
    {
        $db = \Config\Database::connect();

        $comptes = $db->table('Compte')->where('id_client', $id_client)->get()->getResultArray();
        $idsComptes = array_column($comptes, 'id_compte');

        if (empty($idsComptes)) {
            return [];
        }

        $resultats = $db->table('Operation')
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
            ->limit($limite)
            ->get()
            ->getResultArray();

        foreach ($resultats as &$op) {
            $op['est_source'] = in_array($op['id_compte_source'], $idsComptes, true);
        }

        return $resultats;
    }

    /**
     * Historique de toutes les operations touchant un compte precis (source OU
     * destination), utilise cote operateur pour la fiche detail d'un compte.
     */
    public function getHistoriqueParCompte(int $idCompte, int $limite = 100): array
    {
        $db = \Config\Database::connect();

        return $db->table('Operation')
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
                ->where('Operation.id_compte_source', $idCompte)
                ->orWhere('Operation.id_compte_destination', $idCompte)
            ->groupEnd()
            ->orderBy('Operation.date_operation', 'DESC')
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
        $db = \Config\Database::connect();

        $builder = $db->table('Operation')
            ->select('
                OperateurSource.nom AS nom_operateur_source,
                OperateurDestination.nom AS nom_operateur_dest,
                COUNT(Operation.id_operation) AS nombre_transferts,
                SUM(Operation.montant) AS montant_total,
                SUM(Operation.frais_appliques) AS frais_total
            ')
            ->join('Compte AS CompteSource', 'CompteSource.id_compte = Operation.id_compte_source')
            ->join('Client AS ClientSource', 'ClientSource.id_client = CompteSource.id_client')
            ->join('Operateur AS OperateurSource', 'OperateurSource.id_operateur = ClientSource.id_operateur')
            ->join('Compte AS CompteDestination', 'CompteDestination.id_compte = Operation.id_compte_destination')
            ->join('Client AS ClientDestination', 'ClientDestination.id_client = CompteDestination.id_client')
            ->join('Operateur AS OperateurDestination', 'OperateurDestination.id_operateur = ClientDestination.id_operateur')
            ->where('Operation.id_type_operation', self::TYPE_TRANSFERT)
            ->where('Operation.id_statut', self::STATUT_REUSSIE)
            ->where('ClientDestination.id_operateur != ClientSource.id_operateur', null, false);

        if ($dateDebut) {
            $builder->where('Operation.date_operation >=', $dateDebut . ' 00:00:00');
        }

        if ($dateFin) {
            $builder->where('Operation.date_operation <=', $dateFin . ' 23:59:59');
        }

        return $builder
            ->groupBy('OperateurSource.nom, OperateurDestination.nom')
            ->orderBy('OperateurSource.nom')
            ->orderBy('OperateurDestination.nom')
            ->get()
            ->getResultArray();
    }
}