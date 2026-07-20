<?php $title = $bareme ? 'Modifier une tranche' : 'Ajouter une tranche'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>
<div class="container">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%); min-height: 100vh; }
        .form-card { max-width: 520px; margin: 4rem auto; background: white; border-radius: 16px;
                     box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .form-card-header { background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
                             color: white; padding: 1.8rem 2rem; }
        .form-card-header h4 { font-weight: 700; margin-bottom: 0.2rem; }
        .form-card-body { padding: 2rem; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #344054; }
        .form-control, .form-select { border-radius: 10px; padding: 0.65rem 0.9rem; border: 1px solid #d9dfe8; }
        .btn-submit { border-radius: 50px; padding: 0.7rem 1.6rem; font-weight: 600; }
    </style>
    <div class="form-card">

        <div class="form-card-header">
            <h4><?= $bareme ? 'Modifier la tranche' : 'Ajouter une tranche' ?></h4>
        </div>

        <div class="form-card-body">

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/operateur/baremeFrais/save" method="post">

                <input type="hidden" name="id_bareme" value="<?= $bareme['id_bareme'] ?? '' ?>">
                <input type="hidden" name="id_operateur" value="<?= $operateurYasId ?>">

                <div class="alert alert-light border mb-3">
                    Opérateur fixé : <strong><?= esc($nomOperateur) ?></strong>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type d'opération</label>
                    <select class="form-select" name="id_type_operation" required>
                        <?php foreach ($typesOperation as $type): ?>
                            <option value="<?= $type['id_type_operation'] ?>"
                                <?= (isset($bareme) && $bareme['id_type_operation'] == $type['id_type_operation']) ? 'selected' : '' ?>>
                                <?= esc($type['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant min (Ar)</label>
                    <input type="number" class="form-control" name="montant_min"
                           value="<?= old('montant_min', $bareme['montant_min'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant max (Ar)</label>
                    <input type="number" class="form-control" name="montant_max"
                           value="<?= old('montant_max', $bareme['montant_max'] ?? '') ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label">Frais (Ar)</label>
                    <input type="number" class="form-control" name="valeur_frais"
                           value="<?= old('valeur_frais', $bareme['valeur_frais'] ?? '') ?>" required>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="/operateur/baremeFrais" class="btn btn-link text-muted text-decoration-none">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-submit">Enregistrer</button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php $this->endSection(); ?>