<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientModel;
use App\Models\OperationModel;

class ClientController extends BaseController
{
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
            $this->creerSession($client);
            return redirect()->to(base_url('client/solde'));
        }

        $operateur = $this->getOperateurByNumero($numero_telephone);

        if (!$operateur) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Operateur non reconnu pour ce numero.');
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

        // On cree aussi le compte associe, sans quoi le client n'a nulle part ou stocker un solde
        \Config\Database::connect()->table('Compte')->insert([
            'id_client' => $id_client,
            'solde'     => 0,
        ]);

        $this->creerSession($newClient);

        return redirect()->to(base_url('client/solde'));
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

    /**
     * Affiche la page operations (retrait / depot / transfert)
     */
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
     * Traite un retrait d'argent. Les frais sont calcules via BaremeFrais
     * et debites en plus du montant demande.
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

        $operationModel = new OperationModel();
        $frais          = $operationModel->getFrais(OperationModel::TYPE_RETRAIT, $montant);
        $totalDebit     = $montant + $frais;

        if ($compte['solde'] < $totalDebit) {
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
     * Traite un depot d'argent (aucun frais applique, pas de bareme defini pour le depot).
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

        $operationModel = new OperationModel();
        $frais          = $operationModel->getFrais(OperationModel::TYPE_DEPOT, $montant);

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
     * Traite un transfert d'argent vers un autre client. Les frais (via BaremeFrais)
     * sont a la charge de l'expediteur ; le destinataire recoit le montant plein.
     */
    public function transfert()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $montant             = (float) $this->request->getVar('montant');
        $numero_destinataire = trim($this->request->getVar('numero_destinataire'));
        $numero_destinataire = preg_replace('/\D/', '', $numero_destinataire);

        if ($montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if (strlen($numero_destinataire) !== 10 || $numero_destinataire[0] !== '0') {
            return redirect()->back()->with('error', 'Numero du destinataire invalide.');
        }

        $numero_expediteur = session()->get('numero_telephone');

        if ($numero_destinataire === $numero_expediteur) {
            return redirect()->back()->with('error', "Vous ne pouvez pas vous transferer de l'argent a vous-meme.");
        }

        $clientModel  = new ClientModel();
        $destinataire = $clientModel->where('numero_telephone', $numero_destinataire)->first();

        if (!$destinataire) {
            return redirect()->back()->with('error', 'Ce numero ne correspond a aucun client Mobile Money.');
        }

        $id_client = session()->get('id_client');
        $db        = \Config\Database::connect();

        $compteExpediteur   = $db->table('Compte')->where('id_client', $id_client)->get()->getRowArray();
        $compteDestinataire = $db->table('Compte')->where('id_client', $destinataire['id_client'])->get()->getRowArray();

        if (!$compteExpediteur || !$compteDestinataire) {
            return redirect()->back()->with('error', 'Compte introuvable.');
        }

        $operationModel = new OperationModel();
        $frais          = $operationModel->getFrais(OperationModel::TYPE_TRANSFERT, $montant);
        $totalDebit     = $montant + $frais;

        if ($compteExpediteur['solde'] < $totalDebit) {
            return redirect()->back()->with(
                'error',
                'Solde insuffisant : ' . number_format($totalDebit, 0, ',', ' ') . ' Ar necessaires (dont ' . number_format($frais, 0, ',', ' ') . ' Ar de frais).'
            );
        }

        $db->transStart();

        $db->table('Compte')
            ->where('id_compte', $compteExpediteur['id_compte'])
            ->update(['solde' => $compteExpediteur['solde'] - $totalDebit]);

        $db->table('Compte')
            ->where('id_compte', $compteDestinataire['id_compte'])
            ->update(['solde' => $compteDestinataire['solde'] + $montant]);

        $operationModel->insert([
            'id_type_operation'      => OperationModel::TYPE_TRANSFERT,
            'id_compte_source'       => $compteExpediteur['id_compte'],
            'id_compte_destination'  => $compteDestinataire['id_compte'],
            'montant'                => $montant,
            'frais_appliques'        => $frais,
            'id_statut'              => OperationModel::STATUT_REUSSIE,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors du transfert, reessayez.');
        }

        return redirect()->to(base_url('client/solde'))
            ->with('success', 'Transfert de ' . number_format($montant, 0, ',', ' ') . ' Ar vers ' . $numero_destinataire . ' effectue (frais : ' . number_format($frais, 0, ',', ' ') . ' Ar).');
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

    /**
     * Recupere le compte (avec nom + numero du client) a partir de l'id_client
     */
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