<?php $title = 'Situation des gains'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-primary bg-gradient text-white p-4 border-0">
            <h2 class="mb-1 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>Situation gain via les différents frais</h2>
            <div class="opacity-75">Yas : retraits, transferts internes et transferts vers autres opérateurs. Orange / Airtel : commissions reçues sur les transferts envoyés par Yas.</div>
        </div>
        <div class="card-body bg-white p-4">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Date de début</label>
                    <input type="date" class="form-control" name="date_debut" value="<?= esc($dateDebut) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary small text-uppercase">Date de fin</label>
                    <input type="date" class="form-control" name="date_fin" value="<?= esc($dateFin) ?>">
                </div>
                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-primary rounded-pill fw-semibold">
                        <i class="bi bi-funnel-fill me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 bg-primary-subtle mb-4">
        <div class="card-body p-4 text-center">
            <div class="text-secondary text-uppercase small fw-semibold mb-1">Total général de tous les opérateurs</div>
            <div class="display-5 fw-bold text-primary mb-0">
                <?= number_format($gainGlobal, 0, ',', ' ') ?> Ar
            </div>
        </div>
    </div>

    <?php if (empty($operateurs)): ?>

        <div class="alert alert-light border rounded-4 text-center mb-0 py-4">
            <i class="bi bi-inbox d-block mb-2 fs-2 text-secondary"></i>
            Aucune opération sur cette période.
        </div>

    <?php else: ?>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($operateurs as $operateur): ?>
                <?php
                $estYas = $operateur['nom_operateur'] === 'Yas';
                $icones = [
                    'retrait'          => 'bi-cash-stack',
                    'meme_operateur'   => 'bi-arrow-left-right',
                    'autre_operateur'  => 'bi-send-fill',
                    'commission_recue' => 'bi-piggy-bank-fill',
                ];
                $clesAffichees = $estYas
                    ? ['retrait', 'meme_operateur', 'autre_operateur']
                    : ['commission_recue'];
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm rounded-4 overflow-hidden <?= $estYas ? 'border-primary border-2' : 'border-0' ?>">
                        <div class="card-header bg-primary bg-gradient text-white p-3 border-0 d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><i class="bi bi-building me-2"></i><?= esc($operateur['nom_operateur']) ?></span>
                            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fs-6">
                                <?= number_format($operateur['total'], 0, ',', ' ') ?> Ar
                            </span>
                        </div>

                        <div class="card-body bg-white p-4">
                            <?php foreach ($clesAffichees as $i => $cle): ?>
                                <?php $section = $operateur['sections'][$cle]; ?>
                                <div class="d-flex align-items-center gap-3 <?= $i > 0 ? 'mt-3 pt-3 border-top' : '' ?>">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary fs-5 p-2 flex-shrink-0">
                                        <i class="bi <?= $icones[$cle] ?>"></i>
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="text-secondary small"><?= esc($section['label']) ?></div>
                                        <div class="fw-bold text-dark fs-5"><?= number_format($section['total'], 0, ',', ' ') ?> Ar</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php $this->endSection(); ?>