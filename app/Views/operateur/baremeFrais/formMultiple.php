<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter des tranches de frais</title>
    <style>
        table { border-collapse: collapse; margin-bottom: 15px; }
        td, th { border: 1px solid #ccc; padding: 6px; }
        input { width: 120px; }
    </style>
</head>
<body>

    <h2>Ajouter des tranches de frais</h2>
    <?php if (session()->getFlashdata('errors')): ?>
        <div style="color: red;">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/operateur/baremeFrais/saveMultiple" method="post">

        <label>Opérateur :</label>
        <select name="id_operateur" required>
            <?php foreach ($operateurs as $op): ?>
                <option value="<?= $op['id_operateur'] ?>"><?= esc($op['nom']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Type d'opération :</label>
        <select name="id_type_operation" required>
            <?php foreach ($typesOperation as $type): ?>
                <option value="<?= $type['id_type_operation'] ?>"><?= esc($type['libelle']) ?></option>
            <?php endforeach; ?>
        </select>

        <table id="table-tranches">
            <thead>
                <tr>
                    <th>Montant min</th>
                    <th>Montant max</th>
                    <th>Frais</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="number" name="montant_min[]" required></td>
                    <td><input type="number" name="montant_max[]" required></td>
                    <td><input type="number" name="valeur_frais[]" required></td>
                    <td><button type="button" onclick="supprimerLigne(this)">Supprimer</button></td>
                </tr>
            </tbody>
        </table>

        <button type="button" onclick="ajouterLigne()">+ Ajouter une ligne</button>
        <br><br>
        <button type="submit">Enregistrer toutes les tranches</button>
    </form>

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