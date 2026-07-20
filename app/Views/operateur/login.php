<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion opérateur - Mobile Money</title>
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9edf5 100%);
        }

        .login-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            max-width: 460px;
            width: 100%;
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.14);
        }

        .login-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            padding: 2rem;
        }

        .login-header .eyebrow {
            font-size: 0.78rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.75;
            font-weight: 700;
        }

        .login-body {
            padding: 2rem;
            background: white;
        }

        .login-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.12);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            margin-bottom: 1rem;
        }

        .btn-enter {
            border-radius: 14px;
            padding: 0.8rem 1rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2c7be5 0%, #1a56b0 100%);
            border: none;
        }

        .switch-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 600;
            text-decoration: none;
            color: #1a56b0;
        }

        .switch-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-shell">
    <div class="card login-card">
        <div class="login-header">
            <div class="login-icon">⚙</div>
            <div class="eyebrow">Espace opérateur</div>
            <h3 class="mt-2 mb-2 fw-bold">Connexion opérateur</h3>
            <p class="mb-0 text-white-50">Accède à la gestion des barèmes, préfixes et gains.</p>
        </div>

        <div class="login-body">
            <div class="alert alert-info border-0 rounded-3 mb-4">
                Cette interface sert de point d'entrée vers l'espace opérateur.
            </div>

            <div class="d-grid">
                <a href="<?= base_url('operateur/prefixe') ?>" class="btn btn-primary btn-enter">
                    Entrer dans l'espace opérateur
                </a>
            </div>

            <div class="text-center mt-4">
                <a href="<?= base_url('client/login') ?>" class="switch-link">
                    ← Basculer en mode client
                </a>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>