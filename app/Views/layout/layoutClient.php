<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $title ?? 'Mobile Money' ?></title>

<?= $this->include('composants/headerClient') ?>

<link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">

</head>

<body class="bg-light">

<?= $this->include('composants/navBarClient') ?>


<div class="app-container">

<?= $this->renderSection('content') ?>

</div>


<!-- Bootstrap JS local (necessaire pour les onglets, dropdowns, alertes fermables, etc.) -->
<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

</body>

</html>