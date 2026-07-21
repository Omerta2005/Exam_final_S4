<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Controllers\OperationController;

class ClientController extends BaseController
{
    private const OPERATEUR_YAS_ID = 2;

    private OperationController $operationController;

    public function __construct()
    {
        $this->operationController = new OperationController();
    }

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
        $client      = $clientModel->where('numero_telephone', $numero_telephone)->first();

        if ($client) {
            if ((int) $client['id_operateur'] !== self::OPERATEUR_YAS_ID) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Seuls les clients Yas peuvent se connecter a cet espace.');
            }

            $this->creerSession($client);
            return redirect()->to(base_url('client/solde'));
        }

        $operateur = $this->operationController->getOperateurByNumero($numero_telephone);

        if (!$operateur || (int) $operateur['id_operateur'] !== self::OPERATEUR_YAS_ID) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Seuls les clients Yas peuvent se connecter a cet espace.');
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

        (new CompteModel())->insert([
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

    public function solde()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $compte = (new CompteModel())->getDetailAvecClient(session()->get('id_client'));

        if (!$compte) {
            return view('client/solde', ['erreur' => 'Aucun compte trouve']);
        }

        return view('client/solde', ['compte' => $compte]);
    }

    public function operations()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $compte = (new CompteModel())->getDetailAvecClient(session()->get('id_client'));

        return view('client/operations', ['solde' => $compte['solde'] ?? 0]);
    }

    public function retrait()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $resultat = $this->operationController->effectuerRetrait(
            session()->get('id_client'),
            session()->get('id_operateur'),
            (float) $this->request->getVar('montant')
        );

        return $this->rediriger($resultat, base_url('client/solde'));
    }

    public function depot()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $resultat = $this->operationController->effectuerDepot(
            session()->get('id_client'),
            session()->get('id_operateur'),
            (float) $this->request->getVar('montant')
        );

        return $this->rediriger($resultat, base_url('client/solde'));
    }

    public function transfert()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $resultat = $this->operationController->effectuerTransfert(
            session()->get('id_client'),
            (int) session()->get('id_operateur'),
            session()->get('numero_telephone'),
            (float) $this->request->getVar('montant_transfert'),
            $this->request->getVar('numero_destinataire') ?? [],
            (bool) $this->request->getVar('inclure_frais')
        );

        return $this->rediriger($resultat, base_url('client/solde'));
    }

    /**
     * Aperçu AJAX (sans ecriture en base) de ce que chaque destinataire recevrait.
     */
    public function calculFrais()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Non connecte']);
        }

        $numeros = json_decode($this->request->getVar('numeros') ?? '[]', true) ?: [];

        $details = $this->operationController->calculerApercuTransfert(
            $numeros,
            (float) $this->request->getVar('montant'),
            (bool) $this->request->getVar('inclure_frais'),
            (int) session()->get('id_operateur')
        );

        return $this->response->setJSON(['details' => $details]);
    }

    public function historique()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('client'));
        }

        $operations = (new OperationModel())->getHistoriqueByClient(session()->get('id_client'));

        return view('client/historique', ['operations' => $operations]);
    }

    /**
     * Traduit un resultat de service (['success' => bool, 'message' => string])
     * en redirect avec le bon flashdata.
     */
    private function rediriger(array $resultat, string $urlSiSucces)
    {
        if ($resultat['success']) {
            return redirect()->to($urlSiSucces)->with('success', $resultat['message']);
        }

        return redirect()->back()->with('error', $resultat['message']);
    }
}