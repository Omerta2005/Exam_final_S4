<?php $title = 'Situation des gains'; ?>
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

        .filter-card {
            border: none;
            border-radius: 14px;
        }
        .filter-card .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #344054;
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
            padding: 0.6rem 1.2rem;
        }

        .total-card {
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            color: white;
        }
        .total-card .total-label {
            opacity: 0.85;
            font-size: 0.95rem;
        }
        .total-card .total-value {
            font-size: 2.2rem;
            font-weight: 700;
        }

        .section-card {
            border: none;
            border-radius: 14px;
            overflow: hidden;
            transition: box-shadow 0.15s ease;
        }
        .section-card:hover {
            box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important;
        }
        .section-header {
            background: #f8fafc;
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #eef1f6;
        }
        .section-title {
            font-weight: 700;
            color: #1a2b4a;
        }
        .section-total {
            font-weight: 700;
            border-radius: 50px;
            padding: 0.45em 1em;
            background: linear-gradient(135deg, #2c7be5, #6ea8fe);
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
        .empty-section {
            padding: 2rem;
            text-align: center;
            color: #98a2b3;
        }
    </style>

    <div class="page-header mb-4">
        <h2 class="mb-1"><i class="bi bi-graph-up-arrow me-2"></i>Situation gain via les différents frais</h2>
        <div class="subtitle">
            Vue filtrée sur <?= esc($nomOperateur) ?>, avec séparation entre transferts internes et vers autres opérateurs
        </div>
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

    <div class="card total-card shadow-sm mb-4">
        <div class="card-body p-4 text-center">
            <div class="total-label mb-1">Total général pour <?= esc($nomOperateur) ?></div>
            <div class="total-value"><?= number_format($gainGlobal, 0, ',', ' ') ?> Ar</div>
        </div>
    </div>

    <?php if (empty($groupes)): ?>

        <div class="empty-state text-center">
            <i class="bi bi-inbox d-block mb-3"></i>
            <h5 class="text-muted">Aucune opération sur cette période</h5>
            <p class="text-muted mb-0">Essaie d'élargir la période sélectionnée pour voir des résultats.</p>
        </div>

    <?php else: ?>

        <?php foreach ($groupes as $key => $section): ?>
            <div class="card section-card shadow-sm mb-4">
                <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="section-title"><i class="bi bi-cash-coin me-2 text-primary"></i><?= esc($section['label']) ?></span>
                    <span class="badge section-total text-white"><?= number_format($section['total'], 0, ',', ' ') ?> Ar</span>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($section['lignes'])): ?>
                        <div class="empty-section">
                            <i class="bi bi-dash-circle d-block mb-2" style="font-size:1.5rem;"></i>
                            Aucune donnée pour cette section.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-clean mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Nombre d'opérations</th>
                                        <th class="text-end">Total frais</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($section['lignes'] as $ligne): ?>
                                        <tr>
                                            <td class="fw-semibold"><?= esc(ucfirst($ligne['type_operation'])) ?></td>
                                            <td><?= (int) $ligne['nombre_operations'] ?></td>
                                            <td class="text-end fw-semibold text-primary"><?= number_format($ligne['total_frais'], 0, ',', ' ') ?> Ar</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
<?php $this->endSection(); ?>