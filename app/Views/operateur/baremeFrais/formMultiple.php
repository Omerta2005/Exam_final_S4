<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter des tranches de frais</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
            min-height: 100vh;
        }
        .form-card {
            max-width: 780px;
            margin: 3rem auto;
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
            padding: 0.6rem 0.9rem;
            border: 1px solid #d9dfe8;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.2rem rgba(44,123,229,0.15);
        }

        .tranches-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }
        .tranches-table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #667085;
            font-weight: 600;
            padding: 0 0.5rem 0.4rem;
            text-align: left;
        }
        .tranches-table tbody tr {
            background: #f8fafc;
        }
        .tranches-table tbody td {
            padding: 0.5rem;
            vertical-align: middle;
        }
        .tranches-table tbody td:first-child {
            border-radius: 10px 0 0 10px;
        }
        .tranches-table tbody td:last-child {
            border-radius: 0 10px 10px 0;
        }
        .tranches-table input {
            border-radius: 8px;
            border: 1px solid #d9dfe8;
            padding: 0.45rem 0.6rem;
            width: 100%;
        }
        .tranches-table input:focus {
            outline: none;
            border-color: #2c7be5;
            box-shadow: 0 0 0 0.15rem rgba(44,123,229,0.15);
        }

        .btn-supprimer {
            border: none;
            background: transparent;
            color: #dc3545;
            font-size: 1.1rem;
            padding: 0.3rem 0.5rem;
        }
        .btn-supprimer:hover { color: #a71d2a; }

        .btn-ajouter-ligne {
            border-radius: 50px;
            font-weight: 500;
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
</head>
<body>

    <div class="container">
        <div class="form-card">

            <div class="form-card-header">
                <h4><i class="bi bi-layers-fill me-2"></i>Ajouter des tranches de frais</h4>
                <div class="subtitle">Définis plusieurs tranches en une seule fois pour un opérateur et un type d'opération</div>
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

                <form action="/operateur/baremeFrais/saveMultiple" method="post">

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Opérateur</label>
                            <select class="form-select" name="id_operateur" required>
                                <?php foreach ($operateurs as $op): ?>
                                    <option value="<?= $op['id_operateur'] ?>"><?= esc($op['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type d'opération</label>
                            <select class="form-select" name="id_type_operation" required>
                                <?php foreach ($typesOperation as $type): ?>
                                    <option value="<?= $type['id_type_operation'] ?>"><?= esc($type['libelle']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <label class="form-label mb-2">Tranches de montant</label>

                    <table class="tranches-table" id="table-tranches">
                        <thead>
                            <tr>
                                <th>Montant min (Ar)</th>
                                <th>Montant max (Ar)</th>
                                <th>Frais (Ar)</th>
                                <th style="width:40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="number" name="montant_min[]" required></td>
                                <td><input type="number" name="montant_max[]" required></td>
                                <td><input type="number" name="valeur_frais[]" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn-supprimer" onclick="supprimerLigne(this)" title="Supprimer la ligne">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-outline-primary btn-sm btn-ajouter-ligne mb-4" onclick="ajouterLigne()">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter une ligne
                    </button>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="/operateur/baremeFrais" class="btn btn-link btn-cancel text-muted text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check2 me-1"></i>Enregistrer toutes les tranches
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <script>
        function ajouterLigne() {
            const tbody = document.querySelector('#table-tranches tbody');
            const ligne = tbody.rows[0].cloneNode(true);
            ligne.querySelectorAll('input').forEach(input => input.value = '');
            tbody.appendChild(ligne);
        }

        function supprimerLigne(btn) {
            const tbody = document.querySelector('#table-tranches tbody');
            if (tbody.rows.length > 1) {
                btn.closest('tr').remove();
            }
        }
    </script>

</body>
</html>