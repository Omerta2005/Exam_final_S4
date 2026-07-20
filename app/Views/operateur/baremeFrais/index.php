<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barèmes de frais</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h2 { margin-top: 40px; border-bottom: 2px solid #333; }
        h3 { margin-top: 20px; color: #555; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .btn-ajout { display: inline-block; margin-top: 20px; padding: 8px 14px;
                     background: #2c7be5; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Barèmes de frais</h1>

    <a class="btn-ajout" href="/operateur/baremeFrais/form">Ajouter une tranche</a>

    <?php if (empty($groupes)): ?>
        <p>Aucun barème enregistré pour le moment.</p>
    <?php endif; ?>

    <?php foreach ($groupes as $nomOperateur => $typesOperation): ?>

        <h2><?= esc($nomOperateur) ?></h2>

        <?php foreach ($typesOperation as $libelleType => $tranches): ?>

            <h3><?= esc(ucfirst($libelleType)) ?></h3>

            <table>
                <thead>
                    <tr>
                        <th>Montant min</th>
                        <th>Montant max</th>
                        <th>Frais</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tranches as $tranche): ?>
                        <tr>
                            <td><?= number_format($tranche['montant_min'], 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($tranche['montant_max'], 0, ',', ' ') ?> Ar</td>
                            <td><?= number_format($tranche['valeur_frais'], 0, ',', ' ') ?> Ar</td>
                            <td>
                                <a href="/operateur/baremeFrais/form?id=<?= $tranche['id_bareme'] ?>">Modifier</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endforeach; ?>

    <?php endforeach; ?>

</body>
</html>