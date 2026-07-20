<?php $title = 'Comptes clients'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">
    <div class="card border-0 shadow-lg mb-4">
        <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
            <h2 class="mb-1 fw-bold">Comptes clients</h2>
            <div class="opacity-75"><?= count($comptes) ?> client<?= count($comptes) > 1 ? 's' : '' ?></div>
        </div>
    </div>

    <?php if (empty($comptes)): ?>
        <div class="alert alert-light border text-center py-4 mb-0">
            Aucun compte client trouvé.
        </div>
    <?php else: ?>
        <div class="list-group shadow-sm rounded-4 overflow-hidden">
            <?php foreach ($comptes as $compte): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    <div>
                        <div class="fw-bold fs-6"><?= esc($compte['numero_telephone']) ?></div>
                        <div class="text-muted small">
                            <?= esc($compte['nom'] ?: 'Sans nom') ?> · <?= esc($compte['nom_operateur']) ?>
                        </div>
                    </div>

                    <div class=" px-3 py-2 fs-6">
                        <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php $this->endSection(); ?>