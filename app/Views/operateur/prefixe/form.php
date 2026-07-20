<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de préfixe</title>
</head>
<body>
    <?php if (session()->getFlashdata('errors')): ?>
        <div style="color: red; padding: 10px;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <?= esc($error) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/operateur/prefixe/save" method="post">
        <label for="code">Code</label>
        <input type="text" name="code" value="<?= old('code', $prefixe['code'] ?? '') ?>" required>

        <label for="actif">Actif</label>
        <input type="checkbox" name="actif" value="1" <?= $prefixe && $prefixe['actif'] ? 'checked' : '' ?>>

        <label for="id_operateur">Opérateur</label>
        <select name="id_operateur" required>
            <option value="">Sélectionner un opérateur</option>
            <?php foreach ($operateurs as $operateur): ?>
                <option value="<?= $operateur['id_operateur'] ?>" <?= $prefixe && $prefixe['id_operateur'] == $operateur['id_operateur'] ? 'selected' : '' ?>>
                    <?= $operateur['nom'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <input type="hidden" name="id_prefixe" value="<?= $prefixe ? $prefixe['id_prefixe'] : '' ?>">
        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>