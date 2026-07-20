<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des préfixes</title>
</head>
<body>

<h2>Liste des préfixes</h2>

<button>
    <a href="/operateur/prefixe/form" style="color: black; text-decoration: none;">
        Ajouter un préfixe
    </a>
</button>

<table border="1">
    <thead>
        <tr>
            <th>Code</th>
            <th>Actif</th>
            <th>Nom opérateur</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($prefixes as $prefixe): ?>
            <tr>
                <td><?= $prefixe['code'] ?></td>
                <td>
                    <?= $prefixe['actif'] ? 'Oui' : 'Non' ?>
                </td>
                <td><?= $prefixe['nom_operateur'] ?></td>
                <td>
                    <a href="/operateur/prefixe/form?id=<?= $prefixe['id_prefixe'] ?>">
                        Modifier
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>