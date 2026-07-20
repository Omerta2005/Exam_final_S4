<?php $title = 'Situation des gains'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>
<div class="container py-5">

    <div class="page-header mb-4">
        <h2 class="mb-1">Situation des gains</h2>
        <div style="opacity:0.85;">Frais perçus, par opérateur et par type d'opération</div>
    </div>

    <div class="mb-4" style="background:white;border-radius:14px;padding:1.2rem 1.5rem;box-shadow:0 4px 14px rgba(0,0,0,0.05);">
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

    <div class="text-center mb-4" style="background:white;border-radius:16px;padding:2rem;box-shadow:0 4px 14px rgba(0,0,0,0.05);">
        <div class="text-muted mb-1">Total général tous opérateurs confondus</div>
        <div style="font-size:2.2rem;font-weight:700;color:#2c7be5;">
            <?= number_format($gainGlobal, 0, ',', ' ') ?> Ar
        </div>
    </div>

    <?php if (empty($groupes)): ?>
        <p class="text-center text-muted">Aucune opération sur cette période.</p>
    <?php else: ?>

        <?php foreach ($groupes as $nomOperateur => $data): ?>
            <div class="mb-4" style="background:white;border-radius:16px;padding:1.8rem;box-shadow:0 4px 14px rgba(0,0,0,0.05);">

                <div class="d-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom:2px solid #eef1f6;">
                    <span class="fw-bold" style="font-size:1.2rem;color:#1a56b0;"><?= esc($nomOperateur) ?></span>
                    <span class="fw-bold" style="font-size:1.3rem;color:#2c7be5;">
                        <?= number_format($data['total'], 0, ',', ' ') ?> Ar
                    </span>
                </div>

                <?php foreach ($data['lignes'] as $ligne): ?>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f3f7;">
                        <div>
                            <span class="fw-semibold"><?= esc(ucfirst($ligne['type_operation'])) ?></span>

                            <?php if ($ligne['portee'] === 'meme_operateur'): ?>
                                <span class="badge bg-primary-subtle text-primary ms-2">Même opérateur</span>
                            <?php elseif ($ligne['portee'] === 'autre_operateur'): ?>
                                <span class="badge bg-warning-subtle text-warning ms-2">Vers autre opérateur</span>
                            <?php endif; ?>

                            <span class="text-muted ms-2" style="font-size:0.82rem;">
                                <?= $ligne['nombre_operations'] ?> opération<?= $ligne['nombre_operations'] > 1 ? 's' : '' ?>
                            </span>
                        </div>
                        <div class="fw-bold"><?= number_format($ligne['total_frais'], 0, ',', ' ') ?> Ar</div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
<?php $this->endSection(); ?>