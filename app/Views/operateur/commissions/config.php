<?php $title = 'Configuration de la commission'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">

    <div class="card border-0 shadow-lg mb-4">
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2 class="mb-1 fw-bold">Configuration de la commission Yas</h2>
                    <div class="opacity-75">
                        S'applique en plus des frais fixes lorsqu'un client Yas transfère vers un autre opérateur (Orange, Airtel, ...).
                    </div>
                </div>
                <a href="<?= base_url('operateur/commissions') ?>" class="btn btn-light rounded-pill px-3">
                    Voir la liste des montants
                </a>
            </div>
        </div>

        <div class="card-body p-4">

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-light border mb-4">
                Cette commission est prélevée en plus des frais fixes pour chaque transfert que Yas envoie vers un autre opérateur.
            </div>

            <?php
            // On n'affiche/configure que la commission de Yas : c'est toujours
            // l'operateur de l'expediteur qui est utilise dans le calcul des frais,
            // et l'expediteur est toujours un client Yas dans cet espace operateur.
            $operateurYas = null;
            foreach ($operateurs as $op) {
                if ($op['nom'] === 'Yas') {
                    $operateurYas = $op;
                    break;
                }
            }
            ?>

            <?php if (! $operateurYas): ?>
                <div class="alert alert-light border text-center mb-0">
                    Opérateur Yas introuvable.
                </div>
            <?php else: ?>

                <form action="/operateur/commissions/save" method="post" class="row g-3 align-items-end">
                    <input type="hidden" name="id_operateur" value="<?= esc($operateurYas['id_operateur']) ?>">

                    <div class="col-md-6">
                        <label class="form-label">Opérateur</label>
                        <input type="text" class="form-control" value="<?= esc($operateurYas['nom']) ?>" disabled>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Pourcentage</label>
                        <div class="input-group">
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="form-control"
                                   name="pourcentage"
                                   value="<?= esc($operateurYas['pourcentage'] * 100) ?>"
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>