<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Espace opérateur' ?></title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
            padding-top: 84px;
        }

        .operator-navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
        }

        .operator-navbar .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .operator-navbar .nav-link {
            color: rgba(255, 255, 255, 0.82);
            font-weight: 600;
            border-radius: 999px;
            padding: 0.55rem 1rem;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .operator-navbar .nav-link:hover,
        .operator-navbar .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.12);
        }

        .operator-brand-subtitle {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1;
        }

        .operator-content {
            padding-bottom: 2rem;
        }
    </style>
</head>
<body>

<?php $currentPath = trim(service('uri')->getPath(), '/'); ?>

<nav class="navbar navbar-expand-lg navbar-dark operator-navbar fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex flex-column align-items-start" href="<?= base_url('operateur/baremeFrais') ?>">
            <span>Espace opérateur</span>
            <span class="operator-brand-subtitle">Gestion des frais, préfixes et gains</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#operatorMenu"
                aria-controls="operatorMenu" aria-expanded="false" aria-label="Basculer la navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="operatorMenu">
            <ul class="navbar-nav ms-auto gap-lg-1 mt-3 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, 'operateur/prefixe') ? 'active' : '' ?>"
                       href="<?= base_url('operateur/prefixe') ?>">Préfixes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, 'operateur/baremeFrais') ? 'active' : '' ?>"
                       href="<?= base_url('operateur/baremeFrais') ?>">Barèmes de frais</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPath === 'operateur/gains' ? 'active' : '' ?>"
                       href="<?= base_url('operateur/gains') ?>">Gains</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= base_url('/') ?>">Deconnexion</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="operator-content">
    <?= $this->renderSection('content') ?>
</main>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>