<?= $this->extend('layout/layoutClient') ?>


<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-xl-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div>
                            <div class="text-uppercase small fw-semibold" style="letter-spacing:0.08em; opacity:0.75;">Compte client</div>
                            <h2 class="mb-1 fw-bold">Consultation du solde</h2>
                            <div style="opacity:0.8;">Aperçu rapide du compte et des actions disponibles.</div>
                        </div>
                        <div class="text-end">
                            <div class="small" style="opacity:0.75;">Client</div>
                            <div class="fw-semibold"><?= esc(session()->get('nom') ?? 'Client') ?></div>
                            <div class="small" style="opacity:0.75;"><?= esc(session()->get('numero_telephone') ?? '') ?></div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:0.08em;">Solde disponible</div>
                        <div class="display-5 fw-bold mt-2" style="color: #2c7be5;">
                            <?= number_format($compte['solde'], 0, ',', ' ') ?> Ar
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="bg-light rounded-4 p-3 h-100 border">
                                <div class="text-muted small">Statut</div>
                                <div class="fw-semibold text-dark">Actif</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-4 p-3 h-100 border">
                                <div class="text-muted small">Accès</div>
                                <div class="fw-semibold text-dark">Consultation rapide</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-4 p-3 h-100 border">
                                <div class="text-muted small">Mise à jour</div>
                                <div class="fw-semibold text-dark">Temps réel</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="<?= base_url('client/operations') ?>" class="btn btn-primary btn-lg px-4 rounded-3">Faire une opération</a>
                        <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-primary btn-lg px-4 rounded-3">Voir l'historique</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection() ?>