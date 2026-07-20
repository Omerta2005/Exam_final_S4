<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des préfixes</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
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

        .btn-add {
            border-radius: 50px;
            padding: 0.6rem 1.4rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .prefix-card {
            border: none;
            border-radius: 14px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            overflow: hidden;
        }
        .prefix-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.12) !important;
        }
        .prefix-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #2c7be5, #6ea8fe);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }
        .prefix-code {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .badge-status {
            font-weight: 600;
            padding: 0.4em 0.8em;
            border-radius: 50px;
            font-size: 0.75rem;
        }
        .empty-state {
            border-radius: 16px;
            border: 2px dashed #d0d7e2;
            background: white;
            padding: 4rem 2rem;
        }
        .empty-state i {
            font-size: 3rem;
            color: #c3cbdb;
        }
        .btn-modifier {
            border-radius: 50px;
            font-weight: 500;
        }
        .operator-line {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container py-5">

    <div class="page-header d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-sim me-2"></i>Gestion des préfixes</h2>
            <div class="subtitle">
                <?= count($prefixes) ?> préfixe<?= count($prefixes) > 1 ? 's' : '' ?> enregistré<?= count($prefixes) > 1 ? 's' : '' ?>
            </div>
        </div>
        <a href="/operateur/prefixe/form" class="btn btn-light btn-add">
            <i class="bi bi-plus-circle me-1"></i> Ajouter un préfixe
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger d-flex align-items-start gap-2 shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($prefixes)): ?>

        <div class="empty-state text-center">
            <i class="bi bi-inbox d-block mb-3"></i>
            <h5 class="text-muted">Aucun préfixe enregistré</h5>
            <p class="text-muted mb-4">Commence par ajouter le premier préfixe d'un opérateur.</p>
            <a href="/operateur/prefixe/form" class="btn btn-primary btn-add">
                <i class="bi bi-plus-circle me-1"></i> Ajouter un préfixe
            </a>
        </div>

    <?php else: ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($prefixes as $prefixe): ?>
                <div class="col">
                    <div class="card prefix-card h-100 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="prefix-icon">
                                        <i class="bi bi-telephone-fill"></i>
                                    </div>
                                    <div>
                                        <div class="prefix-code"><?= esc($prefixe['code']) ?></div>
                                    </div>
                                </div>
                                <?php if ($prefixe['actif']): ?>
                                    <span class="badge badge-status bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>Actif
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-status bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="bi bi-dash-circle-fill me-1"></i>Inactif
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="operator-line text-muted mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-building"></i>
                                <?= esc($prefixe['nom_operateur']) ?>
                            </div>

                            <a href="/operateur/prefixe/form?id=<?= $prefixe['id_prefixe'] ?>"
                               class="btn btn-outline-primary btn-sm btn-modifier">
                                <i class="bi bi-pencil-square me-1"></i>Modifier
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>