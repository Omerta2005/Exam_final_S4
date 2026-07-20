<?php $title = 'Configuration commission Yas'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
        }
        .form-card {
            max-width: 780px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .form-card-header {
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            color: white;
            padding: 1.8rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .form-card-header h4 { font-weight: 700; margin-bottom: 0.2rem; }
        .form-card-header .subtitle { opacity: 0.85; font-size: 0.9rem; max-width: 480px; }
        .btn-voir-liste {
            border-radius: 50px;
            font-weight: 600;
            white-space: nowrap;
        }
        .form-card-body { padding: 2rem; }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #344054;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #d9dfe8;
        }
        .form-control:focus {
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.2rem rgba(44,123,229,0.15);
        }
        .form-control:disabled {
            background: #f8fafc;
            color: #667085;
        }

        .pourcentage-preview {
            border-radius: 12px;
            background: linear-gradient(135deg, #eef4fd, #f6f9ff);
            border: 1px solid #dbe7fa;
            padding: 1rem 1.25rem;
            font-size: 0.9rem;
            color: #1a56b0;
        }

        .btn-submit {
            border-radius: 50px;
            padding: 0.7rem 1.6rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(44,123,229,0.25);
        }
    </style>

    <div class="form-card">

        <div class="form-card-header">
            <div>
                <h4><i class="bi bi-percent me-2"></i>Configuration de la commission Yas</h4>
                <div class="subtitle">La commission s'applique en plus des frais fixes sur les transferts vers un autre opérateur.</div>
            </div>
            <a href="<?= base_url('operateur/commissions') ?>" class="btn btn-light btn-voir-liste">
                <i class="bi bi-list-ul me-1"></i>Voir la liste des montants
            </a>
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

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-start gap-2 rounded-3 mb-4">
                    <i class="bi bi-check-circle-fill mt-1"></i>
                    <div><?= esc(session()->getFlashdata('success')) ?></div>
                </div>
            <?php endif; ?>

            <form action="/operateur/commissions/save" method="post">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Opérateur</label>
                        <input type="text" class="form-control" value="<?= esc($operateur['nom']) ?>" disabled>
                        <input type="hidden" name="id_operateur" value="<?= esc($operateur['id_operateur']) ?>">
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
                                   value="<?= esc($operateur['pourcentage'] * 100) ?>"
                                   required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check2 me-1"></i>Enregistrer
                        </button>
                    </div>
                </div>

                <div class="pourcentage-preview d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Cette commission sera prélevée en plus des frais fixes pour chaque transfert effectué vers un autre opérateur.</span>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>