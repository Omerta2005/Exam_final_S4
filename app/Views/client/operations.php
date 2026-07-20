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
					<?php if (session()->getFlashdata('error')): ?>
						<div class="alert alert-danger">
							<?= esc(session()->getFlashdata('error')) ?>
						</div>
					<?php endif; ?>

					<!-- Message succes -->
					<?php if (session()->getFlashdata('success')): ?>
						<div class="alert alert-success">
							<?= esc(session()->getFlashdata('success')) ?>
						</div>
					<?php endif; ?>

					<!-- Onglets -->
					<ul class="nav nav-pills nav-fill mb-4 bg-light rounded-3 p-1" id="opTabs" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active rounded-3"
								id="retrait-tab"
								data-bs-toggle="pill"
								data-bs-target="#retrait"
								type="button"
								role="tab">
								Retrait
							</button>
						</li>

						<li class="nav-item" role="presentation">
							<button class="nav-link rounded-3"
								id="depot-tab"
								data-bs-toggle="pill"
								data-bs-target="#depot"
								type="button"
								role="tab">
								Depot
							</button>
						</li>

						<li class="nav-item" role="presentation">
							<button class="nav-link rounded-3"
								id="transfert-tab"
								data-bs-toggle="pill"
								data-bs-target="#transfert"
								type="button"
								role="tab">
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
										required>
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
										required>
								</div>

								<button type="submit" class="btn btn-primary btn-lg w-100 rounded-3">
									Confirmer le depot
								</button>
							</form>
						</div>

						<!-- TRANSFERT -->
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
											required>

										<button
											type="button"
											class="btn btn-outline-danger btn-retirer-destinataire"
											title="Retirer">
											&times;
										</button>
									</div>
								</div>

								<button
									type="button"
									id="btn-ajouter-destinataire"
									class="btn btn-outline-primary btn-sm rounded-3 mb-3">
									+ Ajouter un destinataire
								</button>

								<div class="mb-3">
									<label class="form-label fw-semibold">
										Montant total a transferer (Ar)
									</label>

									<input
										type="number"
										name="montant_transfert"
										id="montant_transfert"
										class="form-control form-control-lg"
										placeholder="10000"
										min="1"
										step="1"
										required>

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
										value="1">

									<label class="form-check-label" for="inclure_frais">
										Inclure les frais de retrait dans le montant envoye
									</label>

									<div class="form-text">
										Chaque destinataire recevra un peu plus, pour que sa part reste intacte apres un futur retrait chez lui.
									</div>
								</div>

								<div id="resume-transfert" class="bg-light border rounded-3 p-3 mb-3 d-none">
									<p class="mb-2 fw-semibold">Resume</p>

									<div class="small text-muted mb-1">
										Montant total saisi :
										<span id="montant-saisi">0</span> Ar
									</div>

									<div class="small text-muted mb-1">
										Nombre de destinataires :
										<span id="nb-destinataires">0</span>
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
	document.addEventListener('DOMContentLoaded', function () {
		const listeDestinataires = document.getElementById('liste-destinataires');
		const boutonAjouter = document.getElementById('btn-ajouter-destinataire');
		const montantInput = document.getElementById('montant_transfert');
		const inclureFrais = document.getElementById('inclure_frais');
		const resume = document.getElementById('resume-transfert');
		const montantSaisi = document.getElementById('montant-saisi');
		const nbDestinataires = document.getElementById('nb-destinataires');
		const detailDestinataires = document.getElementById('detail-destinataires');

		function mettreAJourResume() {
			const lignes = listeDestinataires.querySelectorAll('.ligne-destinataire');
			const nombres = Array.from(lignes)
				.map((ligne) => ligne.querySelector('input[name="numero_destinataire[]"]')?.value.trim())
				.filter(Boolean);

			if (!nombres.length || !montantInput.value) {
				resume.classList.add('d-none');
				return;
			}

			resume.classList.remove('d-none');
			montantSaisi.textContent = new Intl.NumberFormat('fr-FR').format(Number(montantInput.value || 0));
			nbDestinataires.textContent = nombres.length;
			detailDestinataires.innerHTML = nombres.map((numero, index) => {
				const part = Number(montantInput.value || 0) / nombres.length;
				return `<div class="text-muted">Destinataire ${index + 1} : ${numero} - ${new Intl.NumberFormat('fr-FR').format(part)} Ar</div>`;
			}).join('');
		}

		function ajouterLigne() {
			const premiereLigne = listeDestinataires.querySelector('.ligne-destinataire');
			if (!premiereLigne) {
				return;
			}

			const nouvelleLigne = premiereLigne.cloneNode(true);
			const champ = nouvelleLigne.querySelector('input[name="numero_destinataire[]"]');

			if (champ) {
				champ.value = '';
				champ.required = true;
			}

			listeDestinataires.appendChild(nouvelleLigne);
			mettreAJourResume();
		}

		boutonAjouter?.addEventListener('click', ajouterLigne);

		listeDestinataires.addEventListener('click', function (event) {
			const boutonRetirer = event.target.closest('.btn-retirer-destinataire');
			if (!boutonRetirer) {
				return;
			}

			const lignes = listeDestinataires.querySelectorAll('.ligne-destinataire');
			if (lignes.length === 1) {
				const champ = lignes[0].querySelector('input[name="numero_destinataire[]"]');
				if (champ) {
					champ.value = '';
				}
				mettreAJourResume();
				return;
			}

			boutonRetirer.closest('.ligne-destinataire')?.remove();
			mettreAJourResume();
		});

		listeDestinataires.addEventListener('input', mettreAJourResume);
		montantInput?.addEventListener('input', mettreAJourResume);
		inclureFrais?.addEventListener('change', mettreAJourResume);

		mettreAJourResume();
	});
</script>

<?= $this->endSection() ?>