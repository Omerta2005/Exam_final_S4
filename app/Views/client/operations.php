<?= $this->extend('layout/layoutClient') ?>


<?= $this->section('content') ?>


<div class="container py-4">

<div class="row justify-content-center">

<div class="col-md-6 col-lg-5">


<div class="card shadow-lg border-0 rounded-4">

<div class="card-body p-5">


<!-- Infos client -->

<div class="text-center mb-4">

<h3 class="mt-3 fw-bold">
                            Operations
</h3>


<p class="text-muted mb-0">
                            <?= esc(session()->get('nom') ?? 'Client') ?>
</p>

<p class="text-muted small">
                            <?= esc(session()->get('numero_telephone') ?? '') ?>
</p>

</div>


<!-- Solde -->

<div class="bg-light border rounded-3 p-3 text-center mb-4">
<p class="text-muted mb-1 small">Solde disponible</p>
<h2 class="fw-bold text-primary mb-0">
    <?= isset($solde) ? number_format($solde, 0, ',', ' ') . ' Ar' : '-- Ar' ?>
</h2>
</div>


<!-- Message erreur -->

<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger">
    <?= esc(session()->getFlashdata('error')) ?>
</div>
<?php endif; ?>


<!-- Message succes -->

<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success">
    <?= esc(session()->getFlashdata('success')) ?>
</div>
<?php endif; ?>


<!-- Onglets -->

<ul class="nav nav-pills nav-fill mb-4 bg-light rounded-3 p-1" id="opTabs" role="tablist">

<li class="nav-item" role="presentation">
<button class="nav-link active rounded-3" id="retrait-tab" data-bs-toggle="pill"
data-bs-target="#retrait" type="button" role="tab">
        Retrait
</button>
</li>

<li class="nav-item" role="presentation">
<button class="nav-link rounded-3" id="depot-tab" data-bs-toggle="pill"
data-bs-target="#depot" type="button" role="tab">
        Depot
</button>
</li>

<li class="nav-item" role="presentation">
<button class="nav-link rounded-3" id="transfert-tab" data-bs-toggle="pill"
data-bs-target="#transfert" type="button" role="tab">
        Transfert
</button>
</li>

</ul>


<div class="tab-content" id="opTabsContent">


<!-- RETRAIT -->

<div class="tab-pane fade show active" id="retrait" role="tabpanel">

<form action="<?= base_url('client/retrait') ?>" method="post">

<?= csrf_field() ?>

<div class="mb-3">
<label class="form-label fw-semibold">Montant a retirer (Ar)</label>
<input
    type="number"
    name="montant"
    class="form-control form-control-lg"
    placeholder="10000"
    min="1"
    step="1"
    required
>
</div>

<button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
        Confirmer le retrait
</button>

</form>

</div>


<!-- DEPOT -->

<div class="tab-pane fade" id="depot" role="tabpanel">

<form action="<?= base_url('client/depot') ?>" method="post">

<?= csrf_field() ?>

<div class="mb-3">
<label class="form-label fw-semibold">Montant a deposer (Ar)</label>
<input
    type="number"
    name="montant"
    class="form-control form-control-lg"
    placeholder="10000"
    min="1"
    step="1"
    required
>
</div>

<button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
        Confirmer le depot
</button>

</form>

</div>


<!-- TRANSFERT -->

<div class="tab-pane fade" id="transfert" role="tabpanel">

<form action="<?= base_url('client/transfert') ?>" method="post">

<?= csrf_field() ?>

<div class="mb-3">
<label class="form-label fw-semibold">Numero du destinataire</label>
<input
    type="tel"
    name="numero_destinataire"
    class="form-control form-control-lg"
    placeholder="0331234567"
    pattern="0[0-9]{9}"
    required
>
</div>

<div class="mb-3">
<label class="form-label fw-semibold">Montant a transferer (Ar)</label>
<input
    type="number"
    name="montant"
    class="form-control form-control-lg"
    placeholder="10000"
    min="1"
    step="1"
    required
>
</div>

<button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
        Confirmer le transfert
</button>

</form>

</div>


</div>


<!-- Retour -->

<div class="text-center mt-4">
<a href="<?= base_url('client/solde') ?>" class="text-decoration-none">
                        &larr; Retour au solde
</a>
</div>


</div>

</div>


</div>

</div>

</div>


<?= $this->endSection() ?>