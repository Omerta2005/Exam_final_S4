<nav class="navbar navbar-expand-lg navbar-dark bg-primary">

<div class="container">


<a class="navbar-brand fw-bold" 
   href="<?= base_url('/') ?>">

    Mobile Money

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

<a class="nav-link"
href="<?= base_url('client/solde') ?>">

Solde

</a>

</li>


<li class="nav-item">

<a class="nav-link"
href="<?= base_url('client/operations') ?>">

Opérations

</a>

</li>


<li class="nav-item">

<a class="nav-link"
href="<?= base_url('client/historique') ?>">

Historique

</a>

</li>


<li class="nav-item">

<a class="nav-link text-warning"
href="<?= base_url('logout') ?>">

Déconnexion

</a>

</li>


</ul>


</div>


</div>

</nav>