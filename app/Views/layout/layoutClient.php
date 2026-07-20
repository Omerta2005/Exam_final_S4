<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $title ?? 'Mobile Money' ?></title>

<?= $this->include('composants/headerClient') ?>

<link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">

<style>
body {
	background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
	min-height: 100vh;
	padding-top: 84px;
}

.app-container {
	padding-bottom: 2rem;
}
</style>

</head>

<body>

    <?= $this->include('composants/navBarClient') ?>
    <div class="app-container">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS local (necessaire pour les onglets, dropdowns, alertes fermables, etc.) -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>