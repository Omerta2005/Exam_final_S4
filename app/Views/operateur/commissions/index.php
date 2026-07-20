<?php $title = 'Commissions inter-opérateurs'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>
<div class="container py-5">

    <div class="page-header d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="mb-1">Commissions inter-opérateurs</h2>
            <div class="subtitle">Pourcentage additionnel appliqué sur les transferts vers un autre opérateur</div>
        </div>
    </div>

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

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($operateurs as $operateur): ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><?= esc($operateur['nom']) ?></h5>

                        <form action="/operateur/commissions/save" method="post" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="id_operateur" value="<?= $operateur['id_operateur'] ?>">

                            <div class="input-group">
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       class="form-control"
                                       name="pourcentage"
                                       value="<?= esc($operateur['pourcentage'] * 100) ?>"
                                       required>
                                <span class="input-group-text">%</span>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </form>

                        <div class="text-muted mt-2" style="font-size:0.85rem;">
                            Appliqué en plus des frais fixes lors d'un transfert sortant vers un autre opérateur.
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
<?php $this->endSection(); ?>