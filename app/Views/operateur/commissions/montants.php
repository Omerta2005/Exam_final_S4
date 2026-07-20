<?php $title = 'Situation des montants à envoyer'; ?>
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
        .btn-retour {
            border-radius: 50px;
            font-weight: 600;
            white-space: nowrap;
        }

        .filter-card {
            border: none;
            border-radius: 14px;
        }
        .filter-card .form-control {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #d9dfe8;
        }
        .filter-card .form-control:focus {
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.2rem rgba(44,123,229,0.15);
        }
        .btn-filtrer {
            border-radius: 50px;
            font-weight: 600;
        }

        .stat-card {
            border: none;
            border-radius: 14px;
            background: white;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px rgba(0,0,0,0.08) !important;
        }
        .stat-card .stat-label {
            font-size: 0.82rem;
            color: #667085;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .stat-card .stat-value {
            font-weight: 700;
            font-size: 1.4rem;
            color: #1a2b4a;
        }
        .stat-card.stat-total .stat-value { color: #2c7be5; }

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
        .flow-arrow { color: #2c7be5; }

        .empty-state {
            border-radius: 16px;
            border: 2px dashed #d0d7e2;
            background: white;
            padding: 3rem 2rem;
        }
        .empty-state i {
            font-size: 2.5rem;
            color: #c3cbdb;
        }

        .btn-action {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.6rem 1.4rem;
        }
    </style>

    <div class="page-header d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-send-fill me-2"></i>Situation des montants à envoyer à chaque opérateur</h2>
            <div class="subtitle">Vue basée sur les transferts inter-opérateurs réalisés par <?= esc($nomOperateur) ?></div>
        </div>
        <a href="<?= base_url('operateur/commissions') ?>" class="btn btn-light btn-retour">
            <i class="bi bi-arrow-left me-1"></i>Retour commissions
        </a>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Date de début</label>
                    <input type="date" class="form-control" name="date_debut" value="<?= esc($dateDebut) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date de fin</label>
                    <input type="date" class="form-control" name="date_fin" value="<?= esc($dateFin) ?>">
                </div>
                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-primary btn-filtrer">
                        <i class="bi bi-funnel-fill me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-label mb-2"><i class="bi bi-building me-1"></i>Opérateur source</div>
                    <div class="stat-value"><?= esc($nomOperateur) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-label mb-2"><i class="bi bi-list-ol me-1"></i>Nombre de lignes</div>
                    <div class="stat-value"><?= count($lignes) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card stat-total shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="stat-label mb-2"><i class="bi bi-cash-stack me-1"></i>Total à envoyer</div>
                    <div class="stat-value"><?= number_format($totalGeneral, 0, ',', ' ') ?> Ar</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <?php if (empty($lignes)): ?>
                <div class="empty-state text-center m-4">
                    <i class="bi bi-inbox d-block mb-3"></i>
                    <h5 class="text-muted">Aucun transfert inter-opérateur trouvé</h5>
                    <p class="text-muted mb-0">Aucune donnée sur cette période. Essaie d'élargir la plage de dates.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-clean mb-0">
                        <thead>
                            <tr>
                                <th>Opérateur source</th>
                                <th></th>
                                <th>Opérateur destination</th>
                                <th class="text-end">Nombre de transferts</th>
                                <th class="text-end">Montant total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lignes as $ligne): ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($ligne['nom_operateur_source']) ?></td>
                                    <td class="flow-arrow"><i class="bi bi-arrow-right-short fs-5"></i></td>
                                    <td><?= esc($ligne['nom_operateur_dest']) ?></td>
                                    <td class="text-end"><?= (int) $ligne['nombre_transferts'] ?></td>
                                    <td class="text-end fw-semibold text-primary"><?= number_format($ligne['montant_total'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 flex-wrap">
        <a href="<?= base_url('operateur/commissions/config') ?>" class="btn btn-outline-primary btn-action">
            <i class="bi bi-gear-fill me-1"></i>Aller à la configuration
        </a>
        <a href="<?= base_url('operateur/commissions') ?>" class="btn btn-primary btn-action">
            <i class="bi bi-arrow-clockwise me-1"></i>Rafraîchir la liste
        </a>
    </div>
</div>

<?php $this->endSection(); ?>