<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table            = 'Compte';
    protected $primaryKey       = 'id_compte';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_client', 'solde'];

    protected $useTimestamps = false;

    public function findByIdClient(int $idClient): ?array
    {
        return $this->where('id_client', $idClient)->first();
    }

    /**
     * Compte + infos du client proprietaire (nom, numero), utilise pour l'affichage.
     */
    public function getDetailAvecClient(int $idClient): ?array
    {
        return $this->select('Client.numero_telephone, Client.nom, Compte.id_compte, Compte.solde')
            ->join('Client', 'Client.id_client = Compte.id_client')
            ->where('Compte.id_client', $idClient)
            ->first();
    }

    public function crediter(int $idCompte, float $montant): void
    {
        $this->db->table('Compte')
            ->where('id_compte', $idCompte)
            ->set('solde', 'solde + ' . $montant, false)
            ->update();
    }

    public function debiter(int $idCompte, float $montant): void
    {
        $this->db->table('Compte')
            ->where('id_compte', $idCompte)
            ->set('solde', 'solde - ' . $montant, false)
            ->update();
    }

    /**
     * Recherche + jointure Client, utilisee cote operateur (liste/recherche des comptes).
     */
    public function getAllWithClient(?string $recherche = null, ?int $idOperateur = null): array
    {
        $builder = $this->select('Compte.id_compte, Compte.solde, Client.id_client, Client.nom, Client.numero_telephone')
            ->join('Client', 'Client.id_client = Compte.id_client');

        if ($idOperateur !== null) {
            $builder->where('Client.id_operateur', $idOperateur);
        }

        if ($recherche) {
            $builder->groupStart()
                ->like('Client.nom', $recherche)
                ->orLike('Client.numero_telephone', $recherche)
                ->groupEnd();
        }

        return $builder->orderBy('Client.nom')->findAll();
    }

    public function getDetailCompte(int $idCompte, ?int $idOperateur = null): ?array
    {
        $builder = $this->select('Compte.id_compte, Compte.solde, Client.id_client, Client.nom, Client.numero_telephone, Client.id_operateur')
            ->join('Client', 'Client.id_client = Compte.id_client')
            ->where('Compte.id_compte', $idCompte);

        if ($idOperateur !== null) {
            $builder->where('Client.id_operateur', $idOperateur);
        }

        return $builder->first();
    }
}