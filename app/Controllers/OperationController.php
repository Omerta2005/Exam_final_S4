<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\BaremeFraisModel;
use App\Models\ReductionMemeOperateurModel;
use Config\Database;


class OperationController
{
    private ClientModel $clientModel;
    private CompteModel $compteModel;
    private OperationModel $operationModel;
    private BaremeFraisModel $baremeFraisModel;

    public function __construct()
    {
        $this->clientModel      = new ClientModel();
        $this->compteModel      = new CompteModel();
        $this->operationModel   = new OperationModel();
        $this->baremeFraisModel = new BaremeFraisModel();
    }


    public function effectuerRetrait(int $idClient, int $idOperateur, float $montant): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        $compte = $this->compteModel->findByIdClient($idClient);

        if (!$compte) {
            return ['success' => false, 'message' => 'Compte introuvable.'];
        }

        $frais = $this->baremeFraisModel->calculerFrais($idOperateur, OperationModel::TYPE_RETRAIT, $montant);
        $bareme = $this->trouverTranche($idOperateur, OperationModel::TYPE_RETRAIT, $montant);

        if (!$bareme) {
            $this->loggerAnnulation(OperationModel::TYPE_RETRAIT, $compte['id_compte'], null, $montant, 0);
            return ['success' => false, 'message' => 'Aucun bareme de frais defini pour ce montant de retrait. Operation annulee.'];
        }

        $totalDebit = $montant + $frais;

        if ($compte['solde'] < $totalDebit) {
            $this->loggerAnnulation(OperationModel::TYPE_RETRAIT, $compte['id_compte'], null, $montant, $frais);
            return [
                'success' => false,
                'message' => 'Solde insuffisant : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar necessaires (dont ' . number_format($frais, 0, ',', ' ') . ' Ar de frais).',
            ];
        }

        $db = Database::connect();
        $db->transStart();

        $this->compteModel->debiter($compte['id_compte'], $totalDebit);

        $this->operationModel->insert([
            'id_type_operation'     => OperationModel::TYPE_RETRAIT,
            'id_compte_source'      => $compte['id_compte'],
            'id_compte_destination' => null,
            'montant'               => $montant,
            'frais_appliques'       => $frais,
            'id_statut'             => OperationModel::STATUT_REUSSIE,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors du retrait, reessayez.'];
        }

        return [
            'success' => true,
            'message' => 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).',
        ];
    }


    public function effectuerDepot(int $idClient, int $idOperateur, float $montant): array
    {
        if ($montant <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        $compte = $this->compteModel->findByIdClient($idClient);

        if (!$compte) {
            return ['success' => false, 'message' => 'Compte introuvable.'];
        }

        $frais = $this->baremeFraisModel->calculerFrais($idOperateur, OperationModel::TYPE_DEPOT, $montant);

        $db = Database::connect();
        $db->transStart();

        $this->compteModel->crediter($compte['id_compte'], $montant);

        $this->operationModel->insert([
            'id_type_operation'     => OperationModel::TYPE_DEPOT,
            'id_compte_source'      => null,
            'id_compte_destination' => $compte['id_compte'],
            'montant'               => $montant,
            'frais_appliques'       => $frais,
            'id_statut'             => OperationModel::STATUT_REUSSIE,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors du depot, reessayez.'];
        }

        return ['success' => true, 'message' => 'Depot de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue.'];
    }


    public function calculerApercuTransfert(array $numeros, float $montantParPersonne, bool $inclureFrais, int $idOperateurExpediteur): array
    {
        $details = [];

        foreach ($numeros as $numero) {
            $numero    = preg_replace('/\D/', '', $numero);
            $operateur = $this->getOperateurByNumero($numero);

            $memeOperateur = $operateur ? ((int) $operateur['id_operateur'] === $idOperateurExpediteur) : false;

            $fraisRetrait = ($inclureFrais && $operateur)
                ? $this->baremeFraisModel->calculerFrais($operateur['id_operateur'], OperationModel::TYPE_RETRAIT, $montantParPersonne)
                : 0.0;

            $montantEnvoye = $montantParPersonne + $fraisRetrait;


            $fraisTransfertTotal   = 0.0;
            $commissionInterOp     = 0.0;

            if ($operateur) {
                $bareme    = $this->trouverTranche($idOperateurExpediteur, OperationModel::TYPE_TRANSFERT, $montantEnvoye);
                $fraisBase = $bareme ? (float) $bareme['valeur_frais'] : 0.0;

                $fraisTransfertTotal = $this->baremeFraisModel->calculerFrais(
                    $idOperateurExpediteur,
                    OperationModel::TYPE_TRANSFERT,
                    $montantEnvoye,
                    $memeOperateur ? null : (int) $operateur['id_operateur']
                );

                

                $commissionInterOp = $memeOperateur ? 0.0 : max(0.0, $fraisTransfertTotal - $fraisBase);
            }

            $details[] = [
                'numero'                    => $numero,
                'montant_envoye'            => $montantEnvoye,
                'frais_retrait'             => $fraisRetrait,
                'operateur_valide'          => $operateur !== null,
                'meme_operateur'            => $memeOperateur,
                'frais_transfert'           => $fraisTransfertTotal,
                'commission_inter_operateur' => $commissionInterOp,
                'total_a_debiter'           => $montantEnvoye + $fraisTransfertTotal,
            ];
        }

        return $details;
    }

    public function effectuerTransfert(
        int $idClientExpediteur,
        int $idOperateurExpediteur,
        string $numeroExpediteur,
        float $montantTotal,
        array $numerosDestinataires,
        bool $inclureFrais
    ): array {
        $numerosDestinataires = array_values(array_unique(array_filter(array_map(
            fn ($n) => preg_replace('/\D/', '', trim($n)),
            $numerosDestinataires
        ))));

        if ($montantTotal <= 0) {
            return ['success' => false, 'message' => 'Montant invalide.'];
        }

        if (empty($numerosDestinataires)) {
            return ['success' => false, 'message' => 'Ajoutez au moins un destinataire.'];
        }

        foreach ($numerosDestinataires as $numero) {
            if (strlen($numero) !== 10 || $numero[0] !== '0') {
                return ['success' => false, 'message' => 'Numero de destinataire invalide : ' . $numero];
            }
        }

        if (in_array($numeroExpediteur, $numerosDestinataires, true)) {
            return ['success' => false, 'message' => "Vous ne pouvez pas vous transferer de l'argent a vous-meme."];
        }

        $compteExpediteur = $this->compteModel->findByIdClient($idClientExpediteur);

        if (!$compteExpediteur) {
            return ['success' => false, 'message' => 'Compte introuvable.'];
        }

        $nbDestinataires    = count($numerosDestinataires);
        $montantParPersonne = $montantTotal / $nbDestinataires;

        $parts      = [];
        $totalDebit = 0.0;

        foreach ($numerosDestinataires as $numero) {

            $operateurDestinataire = $this->getOperateurByNumero($numero);

            if (!$operateurDestinataire) {
                return ['success' => false, 'message' => 'Ce numero ne correspond a aucun operateur Mobile Money : ' . $numero];
            }

            $estMemeOperateur = ((int) $operateurDestinataire['id_operateur'] === $idOperateurExpediteur);

            if (!$estMemeOperateur && $nbDestinataires > 1) {
                return ['success' => false, 'message' => 'Un transfert vers un autre operateur doit avoir un seul destinataire.'];
            }

            $idOperateurClientDestinataire = (int) $operateurDestinataire['id_operateur'];


            $destinataire = $this->clientModel->where('numero_telephone', $numero)->first();

            if (!$destinataire) {
                $idClientCree = $this->clientModel->insert([
                    'numero_telephone' => $numero,
                    'id_operateur'     => $idOperateurClientDestinataire,
                    'nom'              => 'Client',
                ]);



                if (!$idClientCree) {
                    return ['success' => false, 'message' => 'Impossible de creer le compte du destinataire : ' . $numero];
                }

                $this->compteModel->insert([
                    'id_client' => $idClientCree,
                    'solde'     => 0,
                ]);

                $destinataire = $this->clientModel->find($idClientCree);
            }

            $compteDestinataire = $this->compteModel->findByIdClient($destinataire['id_client']);

            if (!$compteDestinataire) {
                return ['success' => false, 'message' => 'Compte introuvable pour : ' . $numero];
            }

            $fraisRetraitCouvert = $inclureFrais
                ? $this->baremeFraisModel->calculerFrais($idOperateurClientDestinataire, OperationModel::TYPE_RETRAIT, $montantParPersonne)
                : 0.0;


            $montantEnvoye = $montantParPersonne + $fraisRetraitCouvert;

            if ($estMemeOperateur) {
                $bareme = $this->trouverTranche($idOperateurExpediteur, OperationModel::TYPE_TRANSFERT, $montantEnvoye);

                if (!$bareme) {
                    $this->loggerAnnulation(OperationModel::TYPE_TRANSFERT, $compteExpediteur['id_compte'], $compteDestinataire['id_compte'], $montantEnvoye, 0);
                    return ['success' => false, 'message' => 'Aucun bareme de frais de transfert defini pour ce montant. Operation annulee.'];
                }

                $fraisTransfert = (float) $bareme['valeur_frais'];
            } else {
                $fraisTransfert = $this->baremeFraisModel->calculerFrais(
                    $idOperateurExpediteur,
                    OperationModel::TYPE_TRANSFERT,
                    $montantEnvoye,
                    $idOperateurClientDestinataire
                );

                if ($fraisTransfert <= 0) {
                    return ['success' => false, 'message' => 'Aucun bareme de frais de transfert defini pour ce montant. Operation annulee.'];
                }
            }

            $parts[] = [
                'compte_destinataire' => $compteDestinataire,
                'montant_envoye'      => $montantEnvoye,
                'frais_transfert'     => $fraisTransfert,
                'est_meme_operateur'  => $estMemeOperateur,
            ];

            $model = new ReductionMemeOperateurModel;

            $pourcentage = $model->getPourcentage();

            $fraisTransfertFinal = $fraisTransfert * ((float) $pourcentage / 100);

            $totalDebit += $montantEnvoye + $fraisTransfertFinal;
        }

        if ($compteExpediteur['solde'] < $totalDebit) {
            $this->loggerAnnulation(
                OperationModel::TYPE_TRANSFERT,
                $compteExpediteur['id_compte'],
                null,
                $montantTotal,
                $totalDebit - $montantTotal
            );

            return [
                'success' => false,
                'message' => 'Solde insuffisant : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar necessaires pour transferer a ' . $nbDestinataires . ' destinataire(s).',
            ];
        }

        $db = Database::connect();
        $db->transStart();

        $this->compteModel->debiter($compteExpediteur['id_compte'], $totalDebit);

        foreach ($parts as $part) {

            $this->compteModel->crediter($part['compte_destinataire']['id_compte'], $part['montant_envoye']);

            $this->operationModel->insert([
                'id_type_operation'     => OperationModel::TYPE_TRANSFERT,
                'id_compte_source'      => $compteExpediteur['id_compte'],
                'id_compte_destination' => $part['compte_destinataire']['id_compte'],
                'id_operateur_destination' => $idOperateurClientDestinataire,
                'montant'               => $part['montant_envoye'],
                'frais_appliques'       => $part['frais_transfert'],
                'id_statut'             => OperationModel::STATUT_REUSSIE,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors du transfert, reessayez.'];
        }

        return [
            'success' => true,
            'message' => 'Transfert de ' . number_format($montantTotal, 0, ',', ' ') . ' Ar reparti entre ' . $nbDestinataires . ' destinataire(s) effectue.',
        ];
    }


    private function trouverTranche(int $idOperateur, int $idTypeOperation, float $montant): ?array
    {
        return $this->baremeFraisModel
            ->where('id_operateur', $idOperateur)
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();
    }

    private function loggerAnnulation(int $idTypeOperation, ?int $idCompteSource, ?int $idCompteDestination, float $montant, float $frais): void
    {
        $this->operationModel->insert([
            'id_type_operation'     => $idTypeOperation,
            'id_compte_source'      => $idCompteSource,
            'id_compte_destination' => $idCompteDestination,
            'montant'               => $montant,
            'frais_appliques'       => $frais,
            'id_statut'             => OperationModel::STATUT_ANNULEE,
        ]);
    }

    public function getOperateurByNumero(string $numeroTelephone): ?array
    {
        $code = substr($numeroTelephone, 0, 3);

        $db = Database::connect();

        $operateur = $db->table('Prefixe')
            ->select('Operateur.id_operateur, Operateur.nom')
            ->join('Operateur', 'Operateur.id_operateur = Prefixe.id_operateur')
            ->where('Prefixe.code', $code)
            ->where('Prefixe.actif', 1)
            ->get()
            ->getRowArray();

        return $operateur ?: null;
    }
}