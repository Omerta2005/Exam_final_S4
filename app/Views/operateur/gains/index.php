<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des gains</title>
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%); min-height: 100vh; }
        .page-header {
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            border-radius: 16px; color: white; padding: 2rem;
        }
        .filter-card {
            background: white; border-radius: 14px; padding: 1.2rem 1.5rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); margin: -1.5rem auto 2rem;
        }
        .total-card {
            background: white; border-radius: 16px; padding: 2rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); text-align: center; margin-bottom: 2rem;
        }
        .total-amount { font-size: 2.2rem; font-weight: 700; color: #2c7be5; }
        .type-card {
            background: white; border-radius: 14px; padding: 1.5rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05); margin-bottom: 1rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .type-name { font-weight: 600; font-size: 1.1rem; }
        .type-count { color: #667085; font-size: 0.85rem; }
        .type-total { font-weight: 700; font-size: 1.3rem; color: #2c7be5; }
    </style>
</head>
<body>

<div class="container py-5">

    <div class="page-header mb-4">
        <h2 class="mb-1">Situation des gains</h2>
        <div style="opacity:0.85;">Frais perçus sur les retraits et transferts</div>
    </div>

    <div class="filter-card shadow-sm">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Date de début</label>
                <input type="date" class="form-control" name="date_debut" value="<?= esc($dateDebut) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Date de fin</label>
                <input type="date" class="form-control" name="date_fin" value="<?= esc($dateFin) ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
    </div>

    <div class="total-card">
        <div class="text-muted mb-1">Total des gains</div>
        <div class="total-amount"><?= number_format($gainTotal, 0, ',', ' ') ?> Ar</div>
    </div>

    <?php if (empty($gainsParType)): ?>
        <p class="text-center text-muted">Aucune opération sur cette période.</p>
    <?php else: ?>
        <?php foreach ($gainsParType as $ligne): ?>
            <div class="type-card">
                <div>
                    <div class="type-name"><?= esc(ucfirst($ligne['type_operation'])) ?></div>
                    <div class="type-count"><?= $ligne['nombre_operations'] ?> opération<?= $ligne['nombre_operations'] > 1 ? 's' : '' ?></div>
                </div>
                <div class="type-total"><?= number_format($ligne['total_frais'], 0, ',', ' ') ?> Ar</div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>