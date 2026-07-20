<?php $title = 'Situation des gains'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>
<div class="container py-5">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%); min-height: 100vh; }
        .page-header {
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            border-radius: 16px; color: white; padding: 2rem;
        }
        .filter-card {
            background: white; border-radius: 14px; padding: 1.2rem 1.5rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); margin-bottom: 2rem;
        }
        .total-global {
            background: white; border-radius: 16px; padding: 2rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); text-align: center; margin-bottom: 2rem;
        }
        .total-global .amount { font-size: 2.2rem; font-weight: 700; color: #2c7be5; }

        .operateur-block {
            background: white; border-radius: 16px; padding: 1.8rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); margin-bottom: 1.5rem;
        }
        .operateur-title {
            font-weight: 700; font-size: 1.2rem; color: #1a56b0;
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1rem; padding-bottom: 0.8rem; border-bottom: 2px solid #eef1f6;
        }
        .operateur-total { font-size: 1.3rem; font-weight: 700; color: #2c7be5; }

        .type-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 0.7rem 0; border-bottom: 1px solid #f1f3f7;
        }
        .type-row:last-child { border-bottom: none; }
        .type-name { font-weight: 600; }
        .type-count { color: #667085; font-size: 0.82rem; }
        .type-amount { font-weight: 700; color: #344054; }
    </style>

    <div class="page-header mb-4">
        <h2 class="mb-1">Situation des gains</h2>
        <div style="opacity:0.85;">Frais perçus, par opérateur et par type d'opération</div>
    </div>

    <div class="filter-card shadow-sm">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Date de début</label>
                <input type="date" class="form-control" name="date_debut" value="<?= esc($dateDebut) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date de fin</label>
                <input type="date" class="form-control" name="date_fin" value="<?= esc($dateFin) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="total-global">
        <div class="text-muted mb-1">Total général tous opérateurs confondus</div>
        <div class="amount"><?= number_format($gainGlobal, 0, ',', ' ') ?> Ar</div>
    </div>

    <?php if (empty($groupes)): ?>
        <p class="text-center text-muted">Aucune opération sur cette période.</p>
    <?php else: ?>

        <?php foreach ($groupes as $nomOperateur => $data): ?>
            <div class="operateur-block">
                <div class="operateur-title">
                    <span><?= esc($nomOperateur) ?></span>
                    <span class="operateur-total"><?= number_format($data['total'], 0, ',', ' ') ?> Ar</span>
                </div>

                <?php foreach ($data['types'] as $ligne): ?>
                    <div class="type-row">
                        <div>
                            <div class="type-name"><?= esc(ucfirst($ligne['type_operation'])) ?></div>
                            <div class="type-count"><?= $ligne['nombre_operations'] ?> opération<?= $ligne['nombre_operations'] > 1 ? 's' : '' ?></div>
                        </div>
                        <div class="type-amount"><?= number_format($ligne['total_frais'], 0, ',', ' ') ?> Ar</div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
<?php $this->endSection(); ?>