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
        $operateurs = [];

        foreach ($operateurModel->findAll() as $operateur) {
            $operateurs[(int) $operateur['id_operateur']] = [
                'id_operateur'  => (int) $operateur['id_operateur'],
                'nom_operateur' => $operateur['nom'],
                'total'         => 0,
                'sections'      => [
                    'retrait'          => ['label' => 'Retraits', 'lignes' => [], 'total' => 0],
                    'meme_operateur'   => ['label' => 'Transferts internes', 'lignes' => [], 'total' => 0],
                    'autre_operateur'  => ['label' => 'Transferts vers autres opérateurs', 'lignes' => [], 'total' => 0],
                    'commission_recue' => ['label' => 'Commissions reçues', 'lignes' => [], 'total' => 0],
                ],
            ];
        }

        foreach ($operations as $op) {
            $idSource = (int) ($op['id_operateur_source'] ?? 0);
            $idDest   = $op['id_operateur_destination'] !== null ? (int) $op['id_operateur_destination'] : null;
            $type     = $op['type_operation'] ?? '';
            $frais    = (float) $op['frais_appliques'];
            $fraisBase = $op['frais_base'] !== null ? (float) $op['frais_base'] : $frais;

            if (! isset($operateurs[$idSource])) {
                continue;
            }

            $ajouter = function (int $idOp, string $section, float $montantFrais, string $typeLigne) use (&$operateurs) {
                $operateurs[$idOp]['sections'][$section]['lignes'][] = [
                    'type_operation'    => $typeLigne,
                    'nombre_operations' => 1,
                    'total_frais'       => $montantFrais,
                ];
                $operateurs[$idOp]['sections'][$section]['total'] += $montantFrais;
                $operateurs[$idOp]['total'] += $montantFrais;
            };

            if ($type === 'retrait') {
                $ajouter($idSource, 'retrait', $frais, 'retrait');
                continue;
            }

            if ($type === 'transfert' && $idDest === $idSource) {
                $ajouter($idSource, 'meme_operateur', $frais, 'transfert');
                continue;
            }

            if ($type === 'transfert' && $idDest !== null && $idDest !== $idSource) {
                $ajouter($idSource, 'autre_operateur', $fraisBase, 'transfert');

                $commission = $frais - $fraisBase;
                if (isset($operateurs[$idDest]) && $commission > 0) {
                    $ajouter($idDest, 'commission_recue', $commission, 'commission');
                }
            }
        }

        $ordreOperateurs = ['Yas' => 0, 'Orange' => 1, 'Airtel' => 2];
        $operateurs = array_filter($operateurs, static fn (array $o): bool => isset($ordreOperateurs[$o['nom_operateur']]));
        uasort($operateurs, static function (array $a, array $b) use ($ordreOperateurs): int {
            $rangA = $ordreOperateurs[$a['nom_operateur']] ?? 99;
            $rangB = $ordreOperateurs[$b['nom_operateur']] ?? 99;
            return $rangA === $rangB ? strcmp($a['nom_operateur'], $b['nom_operateur']) : $rangA <=> $rangB;
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
