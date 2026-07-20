<?= $this->extend('layout/layoutClient') ?>


<?= $this->section('content') ?>


<div class="container py-4">

<div class="row justify-content-center">

<div class="col-md-7 col-lg-6">


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


<!-- TRANSFERT (multiple, montant divise equitablement) -->

<div class="tab-pane fade" id="transfert" role="tabpanel">

<form action="<?= base_url('client/transfert') ?>" method="post" id="form-transfert">

    <?= csrf_field() ?>

    <label class="form-label fw-semibold mb-2">Destinataires</label>

    <div id="liste-destinataires">

        <div class="input-group mb-2 ligne-destinataire">
            <input
                type="tel"
                name="numero_destinataire[]"
                class="form-control"
                placeholder="0331234567"
                pattern="0[0-9]{9}"
                required
            >
            <button type="button" class="btn btn-outline-danger btn-retirer-destinataire" title="Retirer">
                &times;
            </button>
        </div>

    </div>

    <button type="button" id="btn-ajouter-destinataire" class="btn btn-outline-primary btn-sm rounded-3 mb-3">
        + Ajouter un destinataire
    </button>

    <div class="mb-3">
        <label class="form-label fw-semibold">Montant total a transferer (Ar)</label>
        <input
            type="number"
            name="montant_transfert"
            id="montant_transfert"
            class="form-control form-control-lg"
            placeholder="10000"
            min="1"
            step="1"
            required
        >
        <div class="form-text">
            Ce montant sera divise a parts egales entre tous les destinataires.
        </div>
    </div>

    <div class="form-check mb-3">
        <input
            class="form-check-input"
            type="checkbox"
            id="inclure_frais"
            name="inclure_frais"
            value="1"
        >

        <label class="form-check-label" for="inclure_frais">
            Inclure les frais de retrait dans le montant envoye
        </label>

        <div class="form-text">
            Chaque destinataire recevra un peu plus, pour que sa part reste intacte
            apres un futur retrait chez lui.
        </div>
    </div>

    <div id="resume-transfert" class="bg-light border rounded-3 p-3 mb-3 d-none">
        <p class="mb-2 fw-semibold">Resume</p>
        <div class="small text-muted mb-1">
            Montant total saisi : <span id="montant-saisi">0</span> Ar
        </div>
        <div class="small text-muted mb-1">
            Nombre de destinataires : <span id="nb-destinataires">0</span>
        </div>
        <div id="detail-destinataires" class="small"></div>
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


<script>
document.addEventListener('DOMContentLoaded', () => {

    const listeDestinataires = document.getElementById('liste-destinataires');
    const btnAjouter = document.getElementById('btn-ajouter-destinataire');
    const montantInput = document.getElementById('montant_transfert');
    const checkbox = document.getElementById('inclure_frais');

    const resume = document.getElementById('resume-transfert');
    const montantSaisi = document.getElementById('montant-saisi');
    const nbDestinatairesEl = document.getElementById('nb-destinataires');
    const detailDestinataires = document.getElementById('detail-destinataires');

    let timer = null;

    function getNumeros() {
        return Array.from(document.querySelectorAll('input[name="numero_destinataire[]"]'))
            .map(input => input.value.trim())
            .filter(v => v.length === 10);
    }

    function ajouterLigne() {
        const ligne = listeDestinataires.querySelector('.ligne-destinataire').cloneNode(true);
        ligne.querySelector('input').value = '';
        listeDestinataires.appendChild(ligne);
        attacherRetrait(ligne);
    }

    function attacherRetrait(ligne) {
        const btn = ligne.querySelector('.btn-retirer-destinataire');
        btn.addEventListener('click', () => {
            if (listeDestinataires.querySelectorAll('.ligne-destinataire').length > 1) {
                ligne.remove();
                calculer();
            }
        });
    }

    document.querySelectorAll('.ligne-destinataire').forEach(attacherRetrait);
    btnAjouter.addEventListener('click', ajouterLigne);

    async function calculer() {

        const montantTotal = parseFloat(montantInput.value);
        const numeros = getNumeros();

        if (isNaN(montantTotal) || montantTotal <= 0 || numeros.length === 0) {
            resume.classList.add('d-none');
            return;
        }

        const montantParPersonne = montantTotal / numeros.length;

        const response = await fetch("<?= base_url('client/calcul-frais') ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                montant: montantParPersonne,
                numeros: JSON.stringify(numeros),
                inclure_frais: checkbox.checked ? 1 : 0
            })
        });

        const data = await response.json();

        montantSaisi.textContent = montantTotal.toLocaleString();
        nbDestinatairesEl.textContent = numeros.length;

        detailDestinataires.innerHTML = data.details.map(d => `
            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                <span>${d.numero}</span>
                <span>${d.montant_envoye.toLocaleString()} Ar ${d.frais_retrait > 0 ? '(dont ' + d.frais_retrait.toLocaleString() + ' Ar de frais de retrait couverts)' : ''}</span>
            </div>
        `).join('');

        resume.classList.remove('d-none');
    }

    montantInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(calculer, 500);
    });

    checkbox.addEventListener('change', calculer);

    listeDestinataires.addEventListener('input', (e) => {
        if (e.target.matches('input[name="numero_destinataire[]"]')) {
            clearTimeout(timer);
            timer = setTimeout(calculer, 500);
        }
    });

});
</script>

<?= $this->endSection() ?>