<?= $this->extend('layout/layoutClient') ?>


<?= $this->section('content') ?>


<div class="card shadow">

    <div class="card-body">

        <h2>
            Consultation du solde
        </h2>


        <h1 class="text-primary">
            <?= $compte['solde'] ?> Ar
        </h1>


    </div>

</div>


<?= $this->endSection() ?>