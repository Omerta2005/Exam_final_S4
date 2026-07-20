<?php $title = isset($prefixe) && $prefixe ? 'Modifier un préfixe' : 'Ajouter un préfixe'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>
<div class="container">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
        }
        .form-card {
            max-width: 520px;
            margin: 4rem auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .form-card-header {
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            color: white;
            padding: 1.8rem 2rem;
        }
        .form-card-header h4 { font-weight: 700; margin-bottom: 0.2rem; }
        .form-card-header .subtitle { opacity: 0.85; font-size: 0.9rem; }
        .form-card-body { padding: 2rem; }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #344054;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 0.65rem 0.9rem;
            border: 1px solid #d9dfe8;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.2rem rgba(44,123,229,0.15);
        }

        .switch-wrapper {
            background: #f8fafc;
            border-radius: 10px;
            padding: 0.9rem 1.1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-check-input {
            width: 2.6em;
            height: 1.4em;
        }

        .btn-submit {
            border-radius: 50px;
            padding: 0.7rem 1.6rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(44,123,229,0.25);
        }
        .btn-cancel {
            border-radius: 50px;
            font-weight: 500;
        }
    </style>
    <div class="form-card">

        <div class="form-card-header">
            <h4>
                <i class="bi <?= isset($prefixe) && $prefixe ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2"></i>
                <?= isset($prefixe) && $prefixe ? 'Modifier le préfixe' : 'Ajouter un préfixe' ?>
            </h4>
            <div class="subtitle">
                <?= isset($prefixe) && $prefixe ? 'Mets à jour les informations de ce préfixe' : 'Renseigne les informations du nouveau préfixe' ?>
            </div>
        </div>

        <div class="form-card-body">

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger d-flex align-items-start gap-2 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/operateur/prefixe/save" method="post">

                <input type="hidden" name="id_prefixe" value="<?= isset($prefixe) && $prefixe ? $prefixe['id_prefixe'] : '' ?>">

                <div class="mb-3">
                    <label for="code" class="form-label">Code du préfixe</label>
                    <input type="text" class="form-control" id="code" name="code"
                           placeholder="ex : 033"
                           value="<?= old('code', $prefixe['code'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="id_operateur" class="form-label">Opérateur</label>
                    <select class="form-select" id="id_operateur" name="id_operateur" required>
                        <option value="">Sélectionner un opérateur</option>
                        <?php foreach ($operateurs as $operateur): ?>
                            <option value="<?= $operateur['id_operateur'] ?>"
                                <?= (isset($prefixe) && $prefixe && $prefixe['id_operateur'] == $operateur['id_operateur']) ? 'selected' : '' ?>>
                                <?= esc($operateur['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <div class="switch-wrapper">
                        <div>
                            <div class="form-label mb-0">Statut</div>
                            <div class="text-muted" style="font-size:0.8rem;">Le préfixe peut être utilisé s'il est actif</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="actif" name="actif" value="1"
                                   <?= (!isset($prefixe) || !$prefixe || $prefixe['actif']) ? 'checked' : '' ?>>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="/operateur/prefixe" class="btn btn-link btn-cancel text-muted text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="bi bi-check2 me-1"></i>Enregistrer
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<?php $this->endSection(); ?>