<?php $title = 'Barèmes de frais'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">
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
            white-space: nowrap;
        }

        .type-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: box-shadow 0.15s ease;
        }
        .type-card:hover {
            box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important;
        }
        .type-header {
            background: #f8fafc;
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #eef1f6;
        }
        .type-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2c7be5, #6ea8fe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .type-title {
            font-weight: 700;
            color: #1a2b4a;
            font-size: 1.05rem;
        }
        .badge-count {
            font-weight: 600;
            padding: 0.4em 0.9em;
            border-radius: 50px;
            font-size: 0.75rem;
            background: #eef2f8;
            color: #2c7be5;
        }

        .table-clean thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #667085;
            font-weight: 600;
            border-top: none;
            border-bottom: 1px solid #eef1f6;
        }
        .table-clean tbody td {
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f2f4f8;
        }
        .table-clean tbody tr:last-child td { border-bottom: none; }
        .table-clean tbody tr:hover { background: #f8fafc; }

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
        .empty-state i {
            font-size: 3rem;
            color: #c3cbdb;
        }
    </style>

    <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-layers-fill me-2"></i>Barèmes de frais</h2>
            <div class="subtitle">Tarification configurée uniquement pour <?= esc($nomOperateur) ?></div>
        </div>
        <a href="/operateur/baremeFrais/formMultiple" class="btn btn-light btn-add">
            <i class="bi bi-plus-circle me-1"></i> Ajouter plusieurs tranches
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm rounded-3 mb-4">
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
            <p class="text-muted mb-4">Commence par ajouter des tranches de frais pour un type d'opération.</p>
            <a href="/operateur/baremeFrais/formMultiple" class="btn btn-primary btn-add">
                <i class="bi bi-plus-circle me-1"></i> Ajouter plusieurs tranches
            </a>
        </div>

    <?php else: ?>

        <?php foreach ($groupes as $libelleType => $tranches): ?>
            <?php
            $icones = [
                'depot' => 'bi-download',
                'retrait' => 'bi-upload',
                'transfert' => 'bi-arrow-left-right',
            ];
            $icone = $icones[strtolower($libelleType)] ?? 'bi-list-ul';
            ?>

            <div class="card type-card shadow-sm mb-4">
                <div class="type-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="type-icon">
                            <i class="bi <?= $icone ?>"></i>
                        </div>
                        <span class="type-title"><?= esc(ucfirst($libelleType)) ?></span>
                    </div>
                    <span class="badge-count"><?= count($tranches) ?> tranche<?= count($tranches) > 1 ? 's' : '' ?></span>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-clean mb-0">
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
                                        <td><?= number_format($tranche['montant_min'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($tranche['montant_max'], 0, ',', ' ') ?> Ar</td>
                                        <td class="fw-semibold text-primary"><?= number_format($tranche['valeur_frais'], 0, ',', ' ') ?> Ar</td>
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
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php $this->endSection(); ?>