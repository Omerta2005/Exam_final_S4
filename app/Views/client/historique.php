<?= $this->extend('layout/layoutClient') ?>

<?= $this->section('content') ?>

<div class="container py-4">
	<div class="row justify-content-center">
		<div class="col-lg-9">
			<div class="card shadow-lg border-0 rounded-4">
				<div class="card-body p-4 p-md-5">
				<div class="text-center mb-4">
					<div class="d-inline-flex align-items-center gap-3">
						<img src="<?= base_url('asset/history.svg') ?>"
							alt="Téléphone"
							width="36"
							height="36">

						<h3 class="fw-bold mb-0">
							Historique des opérations
						</h3>
					</div>
				</div>

					<?php if (empty($operations)): ?>

						<p class="text-muted text-center">
							Aucune operation pour le moment.
						</p>

					<?php else: ?>

						<div class="table-responsive">
							<table class="table table-hover align-middle">
								<thead>
									<tr>
										<th>Date</th>
										<th>Type</th>
										<th>Vers</th>
										<th class="text-end">Montant</th>
										<th class="text-end">Frais</th>
										<th>Statut</th>
									</tr>
								</thead>

								<tbody>

									<?php foreach ($operations as $op): ?>

										<?php
											$estSource = (bool) $op['est_source'];

											if ($op['type_libelle'] === 'depot') {
												$signe   = '+';
												$couleur = 'text-success';
												$libelle = 'Depot';
											} elseif ($op['type_libelle'] === 'retrait') {
												$signe   = '-';
												$couleur = 'text-danger';
												$libelle = 'Retrait';
											} else {
												// transfert
												$signe   = $estSource ? '-' : '+';
												$couleur = $estSource ? 'text-danger' : 'text-success';
												$libelle = $estSource ? 'Transfert envoye' : 'Transfert recu';
											}

											$nomContrepartie = $estSource ? $op['nom_destination'] : $op['nom_source'];
											$numeroContrepartie = $estSource ? $op['numero_destination'] : $op['numero_source'];

											$badgeStatut = match ($op['statut_libelle']) {
												'reussie'    => 'bg-success',
												'en attente' => 'bg-warning text-dark',
												'annulee'    => 'bg-danger',
												default      => 'bg-secondary',
											};
										?>

										<tr>
											<td class="text-muted small">
												<?= date('d/m/Y H:i', strtotime($op['date_operation'])) ?>
											</td>

											<td>
												<?= esc($libelle) ?>
											</td>

											<td>
												<?php if (!empty($numeroContrepartie)): ?>
													<div><?= esc($nomContrepartie) ?></div>
													<div class="text-muted small">
														<?= esc($numeroContrepartie) ?>
													</div>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>

											<td class="text-end fw-semibold <?= $couleur ?>">
												<?= $signe ?><?= number_format($op['montant'], 0, ',', ' ') ?> Ar
											</td>

											<td class="text-end text-muted">
												<?= $op['frais_appliques'] > 0
													? number_format($op['frais_appliques'], 0, ',', ' ') . ' Ar'
													: '-' ?>
											</td>

											<td>
												<span class="badge <?= $badgeStatut ?>">
													<?= esc(ucfirst($op['statut_libelle'])) ?>
												</span>
											</td>
										</tr>

									<?php endforeach; ?>

								</tbody>
							</table>
						</div>

					<?php endif; ?>

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