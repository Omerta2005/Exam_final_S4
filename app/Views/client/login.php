<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion - Mobile Money</title>

    <!-- Bootstrap local -->
    <link rel="stylesheet" href="<?= base_url('css/bootstrap.min.css') ?>">

</head>

<body class="bg-light">


<div class="container">

    <div class="row justify-content-center align-items-center vh-100">

        <div class="col-md-5 col-lg-4">


            <div class="card shadow-lg border-0 rounded-4">


                <div class="card-body p-5">


                    <!-- Logo -->

                    <div class="text-center mb-4">

                        <div class="bg-primary text-white rounded-circle 
                                    d-inline-flex align-items-center 
                                    justify-content-center"
                             style="width:60px;height:60px;">

                            <span class="fs-2">
                                ✓
                            </span>

                        </div>


                        <h3 class="mt-3 fw-bold">
                            Mobile Money
                        </h3>


                        <p class="text-muted">
                            Connectez-vous avec votre numéro
                        </p>

                    </div>




                    <!-- Message erreur -->

                    <?php if(session()->getFlashdata('error')): ?>

                        <div class="alert alert-danger">

                            <?= session()->getFlashdata('error') ?>

                        </div>

                    <?php endif; ?>




                    <!-- Formulaire -->


                    <form action="<?= base_url('client/login') ?>" 
                          method="post">


                        <div class="mb-3">


                            <label class="form-label fw-semibold">

                                Numéro de téléphone

                            </label>



                            <input 
                                type="tel"
                                name="numero_telephone"
                                class="form-control form-control-lg"
                                placeholder="0331234567"
                                required
                            >


                        </div>




                        <button type="submit" 
                                class="btn btn-primary btn-lg w-100 rounded-3">


                            Se connecter


                        </button>



                    </form>


                    <div class="text-center mt-4 pt-3 border-top">

                        <a href="<?= base_url('operateur/login') ?>" class="text-decoration-none fw-semibold">
                            Basculer en mode opérateur
                        </a>

                    </div>



                </div>


            </div>



            <p class="text-center text-muted mt-4">

                © 2026 Mobile Money Simulator

            </p>



        </div>


    </div>


</div>



<!-- Bootstrap JS local -->

<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>


</body>

</html>