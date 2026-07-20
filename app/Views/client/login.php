<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Mobile Money</title>

    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-body p-4">

                    <h3 class="text-center mb-4">
                        Mobile Money
                    </h3>

                    <form action="<?= base_url('client/login') ?>" method="post">

                        <div class="mb-3">
                            <label for="numero_telephone" class="form-label">
                                Numéro de téléphone
                            </label>

                            <input 
                                type="tel"
                                class="form-control"
                                id="numero_telephone"
                                name="numero_telephone"
                                placeholder="0331234567"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Se connecter
                        </button>

                    </form>

                </div>
            </div>

        </div>

    </div>
</div>


<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>