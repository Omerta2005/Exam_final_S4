<?php $title = 'Comptes clients'; ?>
<?php $this->extend('layout/layoutOperateur'); ?>
<?php $this->section('content'); ?>

<div class="container py-5">
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

        .search-card {
            border: none;
            border-radius: 14px;
        }
        .search-card input {
            border-radius: 10px;
            padding: 0.6rem 0.9rem;
            border: 1px solid #d9dfe8;
        }
        .search-card input:focus {
            outline: none;
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.2rem rgba(44,123,229,0.15);
        }

        .client-list {
            border-radius: 16px;
            overflow: hidden;
        }
        .client-item {
            background: white;
            border: none !important;
            border-bottom: 1px solid #eef1f6 !important;
            transition: background 0.15s ease;
        }
        .client-item:last-child { border-bottom: none !important; }
        .client-item:hover { background: #f8fafc; }

        .client-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2c7be5, #6ea8fe);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .client-phone {
            font-weight: 700;
            font-size: 1rem;
            color: #1a2b4a;
        }
        .client-meta {
            font-size: 0.85rem;
        }
        .badge-operateur {
            font-weight: 600;
            padding: 0.3em 0.7em;
            border-radius: 50px;
            font-size: 0.72rem;
            background: #eef2f8;
            color: #2c7be5;
        }
        .solde-badge {
            font-weight: 700;
            font-size: 1.05rem;
            padding: 0.5em 1em;
            border-radius: 50px;
            background: #e9f7ef;
            color: #1a7d3c;
            white-space: nowrap;
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
    </style>

    <div class="page-header d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="mb-1"><i class="bi bi-people-fill me-2"></i>Comptes clients</h2>
            <div class="subtitle">
                <?= count($comptes) ?> client<?= count($comptes) > 1 ? 's' : '' ?> enregistré<?= count($comptes) > 1 ? 's' : '' ?> sur tous les opérateurs
            </div>
        </div>
    </div>

    <?php if (empty($comptes)): ?>

        <div class="empty-state text-center">
            <i class="bi bi-person-x d-block mb-3"></i>
            <h5 class="text-muted">Aucun compte client trouvé</h5>
            <p class="text-muted mb-0">Les comptes clients apparaîtront ici dès qu'ils seront créés.</p>
        </div>

    <?php else: ?>

        <div class="list-group client-list shadow-sm">
            <?php foreach ($comptes as $compte): ?>
                <div class="list-group-item client-item d-flex justify-content-between align-items-center flex-wrap gap-3 py-3 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="client-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <div class="client-phone"><?= esc($compte['numero_telephone']) ?></div>
                            <div class="client-meta text-muted d-flex align-items-center gap-2 mt-1">
                                <span><?= esc($compte['nom'] ?: 'Sans nom') ?></span>
                                <span class="badge-operateur">
                                    <i class="bi bi-building me-1"></i><?= esc($compte['nom_operateur']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="solde-badge">
                        <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<?php $this->endSection(); ?>