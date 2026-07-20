<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OperateurModel;
use App\Models\TypeOperationModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BaremeFraisModel;

class BaremeFraisController extends BaseController
{
    public function index()
    {
        $baremeFraisModel = new BaremeFraisModel();
        $baremeFrais = $baremeFraisModel->getAllWithOperateurEtType();

        // Regrouper par opérateur puis par type d'opération pour l'affichage
        $groupes = [];
        foreach ($baremeFrais as $ligne) {
            $groupes[$ligne['nom_operateur']][$ligne['libelle_type_operation']][] = $ligne;
        }

        return view('operateur/baremeFrais/index', [
            'groupes' => $groupes
        ]);
    }

    public function formMultiple()
    {
        $operateurModel = new OperateurModel();
        $typeModel = new TypeOperationModel();

        return view('operateur/baremeFrais/formMultiple', [
            'operateurs' => $operateurModel->findAll(),
            'typesOperation' => $typeModel->getTypesAvecFrais(),
        ]);
    }

    public function saveMultiple()
    {
        $idOperateur = $this->request->getPost('id_operateur');
        $idTypeOperation = $this->request->getPost('id_type_operation');

        $mins   = $this->request->getPost('montant_min');
        $maxs   = $this->request->getPost('montant_max');
        $frais  = $this->request->getPost('valeur_frais');

        $data = [];
        for ($i = 0; $i < count($mins); $i++) {
            // Ignore les lignes vides (ex: ligne ajoutée puis non remplie)
            if ($mins[$i] === '' || $maxs[$i] === '' || $frais[$i] === '') {
                continue;
            }

            $data[] = [
                'id_operateur'      => $idOperateur,
                'id_type_operation' => $idTypeOperation,
                'montant_min'       => $mins[$i],
                'montant_max'       => $maxs[$i],
                'valeur_frais'      => $frais[$i],
            ];
        }

        if (empty($data)) {
            return redirect()->back()->with('errors', ['Aucune tranche valide à enregistrer.']);
        }

        $model = new BaremeFraisModel();

        // Validation manuelle de chevauchement, car insertBatch ne passe pas par $validationRules ligne par ligne
        $erreurs = $this->validerChevauchement($data);
        if (! empty($erreurs)) {
            return redirect()->back()->withInput()->with('errors', $erreurs);
        }

        $model->insertBatch($data);

        return redirect()->to('/operateur/baremeFrais');
    }

    private function validerChevauchement(array $tranches): array
    {
        $erreurs = [];
        // Trie par montant_min pour comparer les tranches consécutives
        usort($tranches, fn($a, $b) => $a['montant_min'] <=> $b['montant_min']);

        for ($i = 0; $i < count($tranches) - 1; $i++) {
            if ($tranches[$i]['montant_max'] >= $tranches[$i + 1]['montant_min']) {
                $erreurs[] = "Chevauchement détecté entre les tranches "
                    . $tranches[$i]['montant_min'] . "-" . $tranches[$i]['montant_max']
                    . " et " . $tranches[$i + 1]['montant_min'] . "-" . $tranches[$i + 1]['montant_max'];
            }
            if ($tranches[$i]['montant_max'] <= $tranches[$i]['montant_min']) {
                $erreurs[] = "Tranche invalide : montant_max doit être supérieur à montant_min.";
            }
        }

        return $erreurs;
    }

    public function form()
    {
        $id = $this->request->getGet('id');

        $baremeModel = new BaremeFraisModel();
        $operateurModel = new OperateurModel();
        $typeModel = new TypeOperationModel();

        $bareme = $id ? $baremeModel->find($id) : null;

        return view('operateur/baremeFrais/form', [
            'bareme'         => $bareme,
            'operateurs'     => $operateurModel->findAll(),
            'typesOperation' => $typeModel->getTypesAvecFrais(),
        ]);
    }

    public function save()
    {
        $model = new BaremeFraisModel();

        $id = $this->request->getPost('id_bareme');

        $data = [
            'id_operateur'      => $this->request->getPost('id_operateur'),
            'id_type_operation' => $this->request->getPost('id_type_operation'),
            'montant_min'       => $this->request->getPost('montant_min'),
            'montant_max'       => $this->request->getPost('montant_max'),
            'valeur_frais'      => $this->request->getPost('valeur_frais'),
        ];

        if ($id) {
            $success = $model->update($id, $data);
        } else {
            $success = $model->insert($data);
        }

        if (! $success) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/operateur/baremeFrais');
    }
}
