<?php $currentPath = trim(service('uri')->getPath(), '/'); ?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);">

<div class="container">


<a class="navbar-brand fw-bold d-flex flex-column align-items-start">

        <span>Yas Money</span>
        <span style="font-size:0.78rem; color: rgba(255,255,255,0.7); line-height:1;">Espace client</span>

</a>



<button class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#menu">

<span class="navbar-toggler-icon"></span>

</button>



<div class="collapse navbar-collapse" id="menu">


<ul class="navbar-nav ms-auto">


<li class="nav-item">

<a class="nav-link <?= $currentPath === 'client/solde' || $currentPath === '' ? 'active' : '' ?>"
href="<?= base_url('client/solde') ?>">

Solde

</a>

</li>


<li class="nav-item">

<a class="nav-link <?= $currentPath === 'client/operations' ? 'active' : '' ?>"
href="<?= base_url('client/operations') ?>">

Opérations

</a>

</li>


<li class="nav-item">

<a class="nav-link <?= $currentPath === 'client/historique' ? 'active' : '' ?>"
href="<?= base_url('client/historique') ?>">

Historique

</a>

</li>


<li class="nav-item">

<a class="nav-link"
href="<?= base_url('client/logout') ?>">

Deconnexion

</a>


</li>


</ul>


</div>


</div>

</nav>