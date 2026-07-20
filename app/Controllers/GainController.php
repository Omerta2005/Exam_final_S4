<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\OperateurModel;
use App\Models\BaremeFraisModel;
use App\Models\CommissionInterOperateurModel;
use App\Models\OperationModel;

class GainController extends BaseController
{
    public function index()
    {
        $dateDebut = $this->request->getGet('date_debut');
        $dateFin   = $this->request->getGet('date_fin');

        $model = new OperationModel();
        $operations = $model->getOperationsPourGains($dateDebut, $dateFin);

        $operateurModel = new OperateurModel();
        $baremeFraisModel = new BaremeFraisModel();
        $commissionModel = new CommissionInterOperateurModel();
        $operateurs = [];

        foreach ($operateurModel->findAll() as $operateur) {
            $operateurs[(int) $operateur['id_operateur']] = [
                'id_operateur' => (int) $operateur['id_operateur'],
                'nom_operateur' => $operateur['nom'],
                'total' => 0,
                'sections' => [
                    'retrait' => [
                        'label' => 'Retraits',
                        'lignes' => [],
                        'total' => 0,
                    ],
                    'meme_operateur' => [
                        'label' => 'Transferts internes',
                        'lignes' => [],
                        'total' => 0,
                    ],
                    'autre_operateur' => [
                        'label' => 'Transferts vers autres opérateurs',
                        'lignes' => [],
                        'total' => 0,
                    ],
                    'commission_recue' => [
                        'label' => 'Commissions reçues',
                        'lignes' => [],
                        'total' => 0,
                    ],
                ],
            ];
        }

        foreach ($operations as $operation) {
            $idOperateurSource = (int) ($operation['id_operateur_source'] ?? 0);
            $idOperateurDestination = (int) ($operation['id_operateur_destination'] ?? 0);
            $typeOperation = $operation['type_operation'] ?? '';
            $montant = (float) ($operation['montant'] ?? 0);
            $fraisAppliques = (float) ($operation['frais_appliques'] ?? 0);

            if (! isset($operateurs[$idOperateurSource])) {
                continue;
            }

            if ($typeOperation === 'retrait') {
                $key = 'retrait';
                $operateurs[$idOperateurSource]['sections'][$key]['lignes'][] = [
                    'type_operation' => 'retrait',
                    'nombre_operations' => 1,
                    'total_frais' => $fraisAppliques,
                ];
                $operateurs[$idOperateurSource]['sections'][$key]['total'] += $fraisAppliques;
                $operateurs[$idOperateurSource]['total'] += $fraisAppliques;
                continue;
            }

            if ($typeOperation === 'transfert' && $idOperateurDestination === $idOperateurSource) {
                $key = 'meme_operateur';
                $operateurs[$idOperateurSource]['sections'][$key]['lignes'][] = [
                    'type_operation' => 'transfert',
                    'nombre_operations' => 1,
                    'total_frais' => $fraisAppliques,
                ];
                $operateurs[$idOperateurSource]['sections'][$key]['total'] += $fraisAppliques;
                $operateurs[$idOperateurSource]['total'] += $fraisAppliques;
                continue;
            }

            if ($typeOperation === 'transfert' && $idOperateurDestination !== 0 && $idOperateurDestination !== $idOperateurSource) {
                $fraisBase = $baremeFraisModel->calculerFraisBase($idOperateurSource, OperationModel::TYPE_TRANSFERT, $montant);
                $commissionDestination = $montant * $commissionModel->getPourcentage($idOperateurDestination);

                $operateurs[$idOperateurSource]['sections']['autre_operateur']['lignes'][] = [
                    'type_operation' => 'transfert',
                    'nombre_operations' => 1,
                    'total_frais' => $fraisBase,
                ];
                $operateurs[$idOperateurSource]['sections']['autre_operateur']['total'] += $fraisBase;
                $operateurs[$idOperateurSource]['total'] += $fraisBase;

                if (isset($operateurs[$idOperateurDestination])) {
                    $operateurs[$idOperateurDestination]['sections']['commission_recue']['lignes'][] = [
                        'type_operation' => 'commission',
                        'nombre_operations' => 1,
                        'total_frais' => $commissionDestination,
                    ];
                    $operateurs[$idOperateurDestination]['sections']['commission_recue']['total'] += $commissionDestination;
                    $operateurs[$idOperateurDestination]['total'] += $commissionDestination;
                }

                continue;
            }

            if ($typeOperation === 'transfert') {
                $key = 'autre_operateur';
                $operateurs[$idOperateurSource]['sections'][$key]['lignes'][] = [
                    'type_operation' => 'transfert',
                    'nombre_operations' => 1,
                    'total_frais' => $fraisAppliques,
                ];
                $operateurs[$idOperateurSource]['sections'][$key]['total'] += $fraisAppliques;
                $operateurs[$idOperateurSource]['total'] += $fraisAppliques;
            }
        }

        $ordreOperateurs = ['Yas' => 0, 'Orange' => 1, 'Airtel' => 2];
        $operateurs = array_filter($operateurs, static fn (array $operateur): bool => isset($ordreOperateurs[$operateur['nom_operateur']]));
        uasort($operateurs, static function (array $a, array $b) use ($ordreOperateurs): int {
            $rangA = $ordreOperateurs[$a['nom_operateur']] ?? 99;
            $rangB = $ordreOperateurs[$b['nom_operateur']] ?? 99;

            if ($rangA === $rangB) {
                return strcmp($a['nom_operateur'], $b['nom_operateur']);
            }

            return $rangA <=> $rangB;
        });
        $gainGlobal = array_sum(array_column($operateurs, 'total'));

        return view('operateur/gains/index', [
            'operateurs' => $operateurs,
            'gainGlobal' => $gainGlobal,
            'dateDebut'  => $dateDebut,
            'dateFin'    => $dateFin,
        ]);
    }
}
