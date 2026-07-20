<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barèmes de frais</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
        }
        .page-header {
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            border-radius: 16px;
            color: white;
            padding: 2rem;
        }
        .page-header h2 { font-weight: 700; }
        .page-header .subtitle { opacity: 0.85; font-size: 0.95rem; }

        .btn-add {
            border-radius: 50px;
            padding: 0.6rem 1.4rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .operateur-block {
            margin-bottom: 2.5rem;
        }
        .operateur-title {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-weight: 700;
            font-size: 1.3rem;
            color: #1a56b0;
            margin-bottom: 1rem;
        }
        .operateur-title .icon-badge {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2c7be5, #6ea8fe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .type-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
            padding: 1.5rem;
            margin-bottom: 1.2rem;
        }
        .type-title {
            font-weight: 600;
            color: #344054;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .type-badge {
            font-size: 0.75rem;
            padding: 0.3em 0.7em;
            border-radius: 50px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        thead th {
            background: #f8fafc;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #667085;
            font-weight: 600;
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #eef1f6;
        }
        tbody td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid #f1f3f7;
            font-size: 0.92rem;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafbfd; }

        .amount { font-weight: 600; color: #344054; }
        .fee-amount { font-weight: 700; color: #2c7be5; }

        .btn-modifier {
            border-radius: 50px;
            font-weight: 500;
        }

        .empty-state {
            border-radius: 16px;
            border: 2px dashed #d0d7e2;
            background: white;
            padding: 4rem 2rem;
        }
        .empty-state i { font-size: 3rem; color: #c3cbdb; }
    </style>
</head>
<body>

<div class="container py-5">

    <div class="page-header d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-cash-coin me-2"></i>Barèmes de frais</h2>
            <div class="subtitle">Grille tarifaire par opérateur et par type d'opération</div>
        </div>
        <a href="/operateur/baremeFrais/formMultiple" class="btn btn-light btn-add">
            <i class="bi bi-plus-circle me-1"></i> Ajouter plusieurs tranches
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($groupes)): ?>

        <div class="empty-state text-center">
            <i class="bi bi-inbox d-block mb-3"></i>
            <h5 class="text-muted">Aucun barème enregistré</h5>
            <p class="text-muted mb-4">Ajoute une première tranche de frais pour commencer.</p>
            <a href="/operateur/baremeFrais/form" class="btn btn-primary btn-add">
                <i class="bi bi-plus-circle me-1"></i> Ajouter une tranche
            </a>
        </div>

    <?php else: ?>

        <?php foreach ($groupes as $nomOperateur => $typesOperation): ?>

            <div class="operateur-block">
                <div class="operateur-title">
                    <span class="icon-badge"><i class="bi bi-building"></i></span>
                    <?= esc($nomOperateur) ?>
                </div>

                <?php foreach ($typesOperation as $libelleType => $tranches): ?>

                    <?php
                        $icones = [
                            'depot'      => 'bi-download',
                            'retrait'    => 'bi-upload',
                            'transfert'  => 'bi-arrow-left-right',
                        ];
                        $icone = $icones[strtolower($libelleType)] ?? 'bi-list-ul';
                    ?>

                    <div class="type-card">
                        <div class="type-title">
                            <i class="bi <?= $icone ?>"></i>
                            <?= esc(ucfirst($libelleType)) ?>
                            <span class="type-badge bg-primary-subtle text-primary ms-2">
                                <?= count($tranches) ?> tranche<?= count($tranches) > 1 ? 's' : '' ?>
                            </span>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Montant min</th>
                                    <th>Montant max</th>
                                    <th>Frais</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tranches as $tranche): ?>
                                    <tr>
                                        <td class="amount"><?= number_format($tranche['montant_min'], 0, ',', ' ') ?> Ar</td>
                                        <td class="amount"><?= number_format($tranche['montant_max'], 0, ',', ' ') ?> Ar</td>
                                        <td class="fee-amount"><?= number_format($tranche['valeur_frais'], 0, ',', ' ') ?> Ar</td>
                                        <td class="text-end">
                                            <a href="/operateur/baremeFrais/form?id=<?= $tranche['id_bareme'] ?>"
                                               class="btn btn-outline-primary btn-sm btn-modifier">
                                                <i class="bi bi-pencil-square me-1"></i>Modifier
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endforeach; ?>
            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>