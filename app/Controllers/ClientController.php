<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\BaremeFraisModel;

class ClientController extends BaseController
{
    private const OPERATEUR_YAS_ID = 2;

    public function index()
    {
        return view('client/login');
    }

    public function login()
    {
        $numero_telephone = trim($this->request->getVar('numero_telephone'));
        $numero_telephone = preg_replace('/\D/', '', $numero_telephone);

        if (strlen($numero_telephone) !== 10 || $numero_telephone[0] !== '0') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Numero de telephone invalide. Format attendu : 0331234567');
        }

        $clientModel = new ClientModel();

        $client = $clientModel->where('numero_telephone', $numero_telephone)->first();

        if ($client) {
            if ((int) $client['id_operateur'] !== self::OPERATEUR_YAS_ID) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Seuls les clients Yas peuvent se connecter à cet espace.');
            }

            $this->creerSession($client);
            return redirect()->to(base_url('client/solde'));
        }

        $operateur = $this->getOperateurByNumero($numero_telephone);

        if (!$operateur) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Seuls les clients Yas peuvent se connecter à cet espace.');
        }

        if ((int) $operateur['id_operateur'] !== self::OPERATEUR_YAS_ID) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Seuls les clients Yas peuvent se connecter à cet espace.');
        }

        $data = [
            'numero_telephone' => $numero_telephone,
            'id_operateur'     => $operateur['id_operateur'],
            'nom'              => 'Client',
        ];

        if (!$clientModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la creation du compte.');
        }

        $id_client = $clientModel->getInsertID();
        $newClient = $clientModel->find($id_client);

        \Config\Database::connect()->table('Compte')->insert([
            'id_client' => $id_client,
            'solde'     => 0,
        ]);

        $this->creerSession($newClient);

        return redirect()->to(base_url('client/solde'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }

    private function creerSession(array $client): void
    {
        session()->regenerate();

        session()->set([
            'id_client'        => $client['id_client'],
            'numero_telephone' => $client['numero_telephone'],
            'nom'              => $client['nom'],
            'id_operateur'     => $client['id_operateur'],
            'isLoggedIn'       => true,
        ]);
    }

    public function getOperateurByNumero($numero_telephone)
    {
        $code = substr($numero_telephone, 0, 3);

        $db = \Config\Database::connect();

        $operateur = $db->table('Prefixe')
            ->select('Operateur.id_operateur, Operateur.nom')
            ->join('Operateur', 'Operateur.id_operateur = Prefixe.id_operateur')
            ->where('Prefixe.code', $code)
            ->where('Prefixe.actif', 1)
            ->get()
            ->getRowArray();

        return $operateur ?: null;
    }

    public function solde()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $compte = $this->getCompteByIdClient(session()->get('id_client'));

        if (!$compte) {
            return view('client/solde', [
                'erreur' => 'Aucun compte trouve'
            ]);
        }

        return view('client/solde', [
            'compte' => $compte
        ]);
    }

    public function operations()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $compte = $this->getCompteByIdClient(session()->get('id_client'));

        return view('client/operations', [
            'solde' => $compte['solde'] ?? 0,
        ]);
    }

    /**
     * Traite un retrait d'argent. L'operation est annulee si aucun bareme de frais
     * n'est defini pour l'operateur du client, ou si le solde est insuffisant.
     */
    public function retrait()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $montant = (float) $this->request->getVar('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $id_client = session()->get('id_client');
        $db        = \Config\Database::connect();

        $compte = $db->table('Compte')->where('id_client', $id_client)->get()->getRowArray();

        if (!$compte) {
            return redirect()->back()->with('error', 'Compte introuvable.');
        }

        $operationModel   = new OperationModel();
        $baremeFraisModel = new BaremeFraisModel();

        $bareme = $baremeFraisModel
            ->where('id_operateur', session()->get('id_operateur'))
            ->where('id_type_operation', OperationModel::TYPE_RETRAIT)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        if (!$bareme) {
            $operationModel->insert([
                'id_type_operation'      => OperationModel::TYPE_RETRAIT,
                'id_compte_source'       => $compte['id_compte'],
                'id_compte_destination'  => null,
                'montant'                => $montant,
                'frais_appliques'        => 0,
                'id_statut'              => OperationModel::STATUT_ANNULEE,
            ]);

            return redirect()->back()->with('error', 'Aucun bareme de frais defini pour ce montant de retrait. Operation annulee.');
        }

        $frais      = (float) $bareme['valeur_frais'];
        $totalDebit = $montant + $frais;

        if ($compte['solde'] < $totalDebit) {
            $operationModel->insert([
                'id_type_operation'      => OperationModel::TYPE_RETRAIT,
                'id_compte_source'       => $compte['id_compte'],
                'id_compte_destination'  => null,
                'montant'                => $montant,
                'frais_appliques'        => $frais,
                'id_statut'              => OperationModel::STATUT_ANNULEE,
            ]);

            return redirect()->back()->with(
                'error',
                'Solde insuffisant : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar necessaires (dont ' . number_format($frais, 0, ',', ' ') . ' Ar de frais).'
            );
        }

        $db->transStart();

        $db->table('Compte')
            ->where('id_compte', $compte['id_compte'])
            ->update(['solde' => $compte['solde'] - $totalDebit]);

        $operationModel->insert([
            'id_type_operation'      => OperationModel::TYPE_RETRAIT,
            'id_compte_source'       => $compte['id_compte'],
            'id_compte_destination'  => null,
            'montant'                => $montant,
            'frais_appliques'        => $frais,
            'id_statut'              => OperationModel::STATUT_REUSSIE,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du retrait, reessayez.');
        }

        return redirect()->to(base_url('client/solde'))
            ->with('success', 'Retrait de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');
    }

    /**
     * Traite un depot d'argent. Contrairement au retrait/transfert, l'absence de
     * bareme n'annule pas l'operation : les frais sont simplement 0.
     */
    public function depot()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $montant = (float) $this->request->getVar('montant');

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $id_client = session()->get('id_client');
        $db        = \Config\Database::connect();

        $compte = $db->table('Compte')->where('id_client', $id_client)->get()->getRowArray();

        if (!$compte) {
            return redirect()->back()->with('error', 'Compte introuvable.');
        }

        $baremeFraisModel = new BaremeFraisModel();
        $frais = $baremeFraisModel->calculerFrais(session()->get('id_operateur'), OperationModel::TYPE_DEPOT, $montant);

        $operationModel = new OperationModel();

        $db->transStart();

        $db->table('Compte')
            ->where('id_compte', $compte['id_compte'])
            ->update(['solde' => $compte['solde'] + $montant]);

        $operationModel->insert([
            'id_type_operation'      => OperationModel::TYPE_DEPOT,
            'id_compte_source'       => null,
            'id_compte_destination'  => $compte['id_compte'],
            'montant'                => $montant,
            'frais_appliques'        => $frais,
            'id_statut'              => OperationModel::STATUT_REUSSIE,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du depot, reessayez.');
        }

        return redirect()->to(base_url('client/solde'))
            ->with('success', 'Depot de ' . number_format($montant, 0, ',', ' ') . ' Ar effectue.');
    }

    /**
     * Calcule, pour un montant et une liste de numeros donnes, ce que chaque
     * destinataire recevra reellement (avec ou sans les frais de retrait couverts).
     * Utilise en AJAX par la vue operations.php pour afficher un resume avant envoi.
     */
    public function calculFrais()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Non connecte']);
        }

        $montantParPersonne = (float) $this->request->getVar('montant');
        $inclureFrais       = (bool) $this->request->getVar('inclure_frais');
        $numeros             = json_decode($this->request->getVar('numeros') ?? '[]', true) ?: [];

        $idOperateurExpediteur = (int) session()->get('id_operateur');

        $baremeFraisModel = new BaremeFraisModel();
        $details = [];

        foreach ($numeros as $numero) {
            $numero = preg_replace('/\D/', '', $numero);

            $operateur = $this->getOperateurByNumero($numero);

            // Il n'y a pas de frais de retrait a couvrir pour les autres operateurs :
            // on ne connait/controle pas leur grille de frais, donc uniquement Yas -> Yas.
            $estMemeOperateur = $operateur && (int) $operateur['id_operateur'] === $idOperateurExpediteur;

            $frais = 0.0;

            if ($inclureFrais && $estMemeOperateur) {
                $frais = $baremeFraisModel->calculerFrais($operateur['id_operateur'], OperationModel::TYPE_RETRAIT, $montantParPersonne);
            }

            $details[] = [
                'numero'            => $numero,
                'montant_envoye'    => $montantParPersonne + $frais,
                'frais_retrait'     => $frais,
                'meme_operateur'    => $estMemeOperateur,
            ];
        }

        return $this->response->setJSON([
            'details' => $details,
        ]);
    }

    /**
     * Traite un transfert vers un ou plusieurs destinataires. Le montant total saisi
     * est divise a parts egales entre chaque destinataire. Si la case "inclure les
     * frais" est cochee, chaque destinataire recoit en plus ses propres frais de
     * retrait projetes (selon SON operateur), pour que sa part nette reste intacte
     * apres un futur retrait. Les frais de transfert (payes par l'expediteur) sont
     * calcules separement, part par part, selon l'operateur de l'expediteur.
     * L'operation est annulee (et loggee) si l'expediteur n'a pas de bareme de
     * transfert, ou si son solde est insuffisant pour couvrir l'ensemble des parts.
     */
    public function transfert()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $montantTotal          = (float) $this->request->getVar('montant_transfert');
        $inclureFrais          = (bool) $this->request->getVar('inclure_frais');
        $numerosDestinataires  = $this->request->getVar('numero_destinataire') ?? [];

        // Nettoyage + suppression des doublons/valeurs vides
        $numerosDestinataires = array_values(array_unique(array_filter(array_map(
            fn ($n) => preg_replace('/\D/', '', trim($n)),
            $numerosDestinataires
        ))));

        if ($montantTotal <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if (empty($numerosDestinataires)) {
            return redirect()->back()->with('error', 'Ajoutez au moins un destinataire.');
        }

        foreach ($numerosDestinataires as $numero) {
            if (strlen($numero) !== 10 || $numero[0] !== '0') {
                return redirect()->back()->with('error', 'Numero de destinataire invalide : ' . $numero);
            }
        }

        $numero_expediteur = session()->get('numero_telephone');

        if (in_array($numero_expediteur, $numerosDestinataires, true)) {
            return redirect()->back()->with('error', "Vous ne pouvez pas vous transferer de l'argent a vous-meme.");
        }

        $id_client = session()->get('id_client');
        $db        = \Config\Database::connect();
        $idOperateurExpediteur = (int) session()->get('id_operateur');

        $compteExpediteur = $db->table('Compte')->where('id_client', $id_client)->get()->getRowArray();

        if (!$compteExpediteur) {
            return redirect()->back()->with('error', 'Compte introuvable.');
        }

        // L'envoi multiple (plusieurs destinataires, montant divise) n'est autorise
        // que vers des numeros du meme operateur (Yas). Un envoi vers un autre
        // operateur reste possible, mais uniquement seul (un destinataire a la fois).
        if (count($numerosDestinataires) > 1) {
            foreach ($numerosDestinataires as $numero) {
                $operateurVerif = $this->getOperateurByNumero($numero);

                if (!$operateurVerif || (int) $operateurVerif['id_operateur'] !== $idOperateurExpediteur) {
                    return redirect()->back()->with(
                        'error',
                        "L'envoi vers plusieurs destinataires n'est possible que si tous les numeros appartiennent au meme operateur (Yas)."
                    );
                }
            }
        }

        $clientModel      = new ClientModel();
        $operationModel   = new OperationModel();
        $baremeFraisModel = new BaremeFraisModel();

        $nbDestinataires    = count($numerosDestinataires);
        $montantParPersonne = $montantTotal / $nbDestinataires;

        // On construit d'abord chaque "part" (destinataire, montant a envoyer, frais)
        // avant de toucher a la base, pour pouvoir tout valider en un bloc.
        $parts      = [];
        $totalDebit = 0.0;

        foreach ($numerosDestinataires as $numero) {

            $operateurDestinataire = $this->getOperateurByNumero($numero);

            if (!$operateurDestinataire) {
                return redirect()->back()->with('error', 'Ce numero ne correspond a aucun operateur Mobile Money : ' . $numero);
            }

            $estMemeOperateur = ((int) $operateurDestinataire['id_operateur'] === $idOperateurExpediteur);

            $destinataire = null;
            $compteDestinataire = null;
            $creerCompteDestinataire = false;

            if ($estMemeOperateur) {
                $destinataire = $clientModel->where('numero_telephone', $numero)->first();

                if (!$destinataire) {
                    return redirect()->back()->with('error', 'Ce numero ne correspond a aucun client Mobile Money : ' . $numero);
                }

                $compteDestinataire = $db->table('Compte')->where('id_client', $destinataire['id_client'])->get()->getRowArray();

                if (!$compteDestinataire) {
                    return redirect()->back()->with('error', 'Compte introuvable pour : ' . $numero);
                }

                if ((int) $destinataire['id_operateur'] !== $idOperateurExpediteur) {
                    return redirect()->back()->with('error', 'Ce numero n\'appartient pas a Yas.');
                }
            } else {
                $destinataire = $clientModel->where('numero_telephone', $numero)->first();

                if ($destinataire && (int) $destinataire['id_operateur'] !== (int) $operateurDestinataire['id_operateur']) {
                    return redirect()->back()->with('error', 'Ce numero appartient deja a un autre operateur.');
                }

                if ($destinataire) {
                    $compteDestinataire = $db->table('Compte')->where('id_client', $destinataire['id_client'])->get()->getRowArray();
                }

                if (! $destinataire || ! $compteDestinataire) {
                    $creerCompteDestinataire = true;
                }
            }

            // Frais de retrait futurs du destinataire, couverts si la case est cochee.
            // Regle : il n'y a pas de frais de retrait a couvrir pour les autres
            // operateurs (on ne gere/connait pas leur grille de frais).
            $fraisRetraitCouvert = 0.0;
            if ($inclureFrais && $estMemeOperateur) {
                $fraisRetraitCouvert = $baremeFraisModel->calculerFrais(
                    $operateurDestinataire['id_operateur'],
                    OperationModel::TYPE_RETRAIT,
                    $montantParPersonne
                );
            }

            $montantEnvoye = $montantParPersonne + $fraisRetraitCouvert;

            if (! $estMemeOperateur) {
                $fraisTransfert = $baremeFraisModel->calculerFrais(
                    $idOperateurExpediteur,
                    OperationModel::TYPE_TRANSFERT,
                    $montantEnvoye,
                    (int) $operateurDestinataire['id_operateur']
                );

                if ($fraisTransfert <= 0) {
                    return redirect()->back()->with('error', 'Aucun bareme de frais de transfert defini pour ce montant. Operation annulee.');
                }
            } else {
                // Meme operateur : on garde le calcul simple, sans commission additionnelle.
                $bareme = $baremeFraisModel
                    ->where('id_operateur', $idOperateurExpediteur)
                    ->where('id_type_operation', OperationModel::TYPE_TRANSFERT)
                    ->where('montant_min <=', $montantEnvoye)
                    ->where('montant_max >=', $montantEnvoye)
                    ->first();

                if (!$bareme) {
                    $operationModel->insert([
                        'id_type_operation'      => OperationModel::TYPE_TRANSFERT,
                        'id_compte_source'       => $compteExpediteur['id_compte'],
                        'id_compte_destination'  => $compteDestinataire['id_compte'],
                        'id_operateur_destination'=> $idOperateurExpediteur,
                        'montant'                => $montantEnvoye,
                        'frais_appliques'        => 0,
                        'id_statut'              => OperationModel::STATUT_ANNULEE,
                    ]);

                    return redirect()->back()->with('error', 'Aucun bareme de frais de transfert defini pour ce montant. Operation annulee.');
                }

                $fraisTransfert = (float) $bareme['valeur_frais'];
            }

            $parts[] = [
                'numero'                   => $numero,
                'id_operateur_destination' => (int) $operateurDestinataire['id_operateur'],
                'destinataire'             => $destinataire,
                'compte_destinataire'      => $compteDestinataire,
                'creer_compte'             => $creerCompteDestinataire,
                'montant_envoye'           => $montantEnvoye,
                'frais_transfert'          => $fraisTransfert,
                'est_meme_operateur'       => $estMemeOperateur,
            ];

            $totalDebit += $montantEnvoye + $fraisTransfert;
        }

        if ($compteExpediteur['solde'] < $totalDebit) {
            // On logue une seule ligne recapitulative de l'annulation (destination
            // ambigue puisqu'il y a plusieurs destinataires, donc laissee a null).
            $operationModel->insert([
                'id_type_operation'      => OperationModel::TYPE_TRANSFERT,
                'id_compte_source'       => $compteExpediteur['id_compte'],
                'id_compte_destination'  => null,
                'id_operateur_destination'=> $idOperateurExpediteur,
                'montant'                => $montantTotal,
                'frais_appliques'        => $totalDebit - $montantTotal,
                'id_statut'              => OperationModel::STATUT_ANNULEE,
            ]);

            return redirect()->back()->with(
                'error',
                'Solde insuffisant : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar necessaires pour transferer a ' . $nbDestinataires . ' destinataire(s).'
            );
        }

        $db->transStart();

        $db->table('Compte')
            ->where('id_compte', $compteExpediteur['id_compte'])
            ->update(['solde' => $compteExpediteur['solde'] - $totalDebit]);

        $soldeExpediteurCourant = $compteExpediteur['solde'] - $totalDebit;

        foreach ($parts as $part) {

            if ($part['creer_compte']) {
                if (! $part['destinataire']) {
                    $clientModel->insert([
                        'numero_telephone' => $part['numero'],
                        'nom'              => null,
                        'id_operateur'     => $part['id_operateur_destination'],
                    ]);

                    $part['destinataire'] = $clientModel->find($clientModel->getInsertID());
                }

                if (! $part['compte_destinataire']) {
                    $db->table('Compte')->insert([
                        'id_client' => $part['destinataire']['id_client'],
                        'solde'     => 0,
                    ]);

                    $part['compte_destinataire'] = $db->table('Compte')
                        ->where('id_client', $part['destinataire']['id_client'])
                        ->get()
                        ->getRowArray();
                }
            }

            if ($part['est_meme_operateur']) {
                $db->table('Compte')
                    ->where('id_compte', $part['compte_destinataire']['id_compte'])
                    ->update(['solde' => $part['compte_destinataire']['solde'] + $part['montant_envoye']]);
            } elseif ($part['compte_destinataire']) {
                $db->table('Compte')
                    ->where('id_compte', $part['compte_destinataire']['id_compte'])
                    ->update(['solde' => $part['compte_destinataire']['solde'] + $part['montant_envoye']]);
            }

            $operationModel->insert([
                'id_type_operation'      => OperationModel::TYPE_TRANSFERT,
                'id_compte_source'       => $compteExpediteur['id_compte'],
                'id_compte_destination'  => $part['est_meme_operateur'] ? $part['compte_destinataire']['id_compte'] : null,
                'id_operateur_destination'=> $part['id_operateur_destination'],
                'montant'                => $part['montant_envoye'],
                'frais_appliques'        => $part['frais_transfert'],
                'id_statut'              => OperationModel::STATUT_REUSSIE,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du transfert, reessayez.');
        }

        return redirect()->to(base_url('client/solde'))
            ->with('success', 'Transfert de ' . number_format($montantTotal, 0, ',', ' ') . ' Ar reparti entre ' . $nbDestinataires . ' destinataire(s) effectue.');
    }

    /**
     * Affiche l'historique des operations du client connecte
     */
    public function historique()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $operationModel = new OperationModel();

        $operations = $operationModel->getHistoriqueByClient(session()->get('id_client'));

        return view('client/historique', [
            'operations' => $operations,
        ]);
    }

    private function getCompteByIdClient(int $id_client)
    {
        $db = \Config\Database::connect();

        return $db->table('Compte')
            ->select('Client.numero_telephone, Client.nom, Compte.id_compte, Compte.solde')
            ->join('Client', 'Client.id_client = Compte.id_client')
            ->where('Compte.id_client', $id_client)
            ->get()
            ->getRowArray();
    }
}