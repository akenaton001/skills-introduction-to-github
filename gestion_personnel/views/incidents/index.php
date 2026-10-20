<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 text-dark fw-bold">Gestion des Incidents</h2>
            <p class="text-muted small mb-0">Suivi des événements, accidents du travail et dégradations matérielles</p>
        </div>
        <a href="index.php?action=incidents_create" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> Déclarer un incident
        </a>
    </div>

    <!-- Statistiques / KPIs -->
    <div class="row g-3 mb-4">
        <?php 
            $totalIncidents = count($incidents);
            $nonResolus = count(array_filter($incidents, fn($i) => in_array($i['statut'], ['Signolé', 'En cours'])));
            $totalCost = array_sum(array_column($incidents, 'impact_financier'));
        ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-exclamation-triangle text-primary fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Incidents</span>
                        <h4 class="fw-bold mb-0"><?= $totalIncidents ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-clock text-warning fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">En attente / Non résolus</span>
                        <h4 class="fw-bold mb-0 text-warning"><?= $nonResolus ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-coins text-danger fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Impact Financier Total</span>
                        <h4 class="fw-bold mb-0 text-danger"><?= number_format($totalCost, 0, ',', ' ') ?> FCFA</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de liste -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Référence & Titre</th>
                            <th>Date & Lieu</th>
                            <th>Type</th>
                            <th>Employé concerné</th>
                            <th>Gravité</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($incidents)): ?>
                            <?php foreach ($incidents as $inc): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark d-block"><?= htmlspecialchars($inc['reference']) ?></span>
                                        <small class="text-muted"><?= htmlspecialchars($inc['titre']) ?></small>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= date('d/m/Y H:i', strtotime($inc['date_incident'])) ?></div>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($inc['lieu'] ?? 'Non spécifié') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($inc['type_incident']) ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($inc['nom'])): ?>
                                            <a href="index.php?action=show&id=<?= $inc['employee_id'] ?>" class="text-decoration-none fw-semibold">
                                                <?= htmlspecialchars($inc['prenom'] . ' ' . $inc['nom']) ?>
                                            </a>
                                            <br><small class="text-muted"><?= htmlspecialchars($inc['matricule']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted italic">Aucun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeGravite = match($inc['gravite']) {
                                                'Faible' => 'bg-info text-dark',
                                                'Moyen' => 'bg-warning text-dark',
                                                'Élevé' => 'bg-danger text-white',
                                                'Critique' => 'bg-dark text-white',
                                                default => 'bg-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeGravite ?>"><?= htmlspecialchars($inc['gravite']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                            $badgeStatut = match($inc['statut']) {
                                                'Signolé' => 'bg-secondary',
                                                'En cours' => 'bg-primary',
                                                'Résolu' => 'bg-success',
                                                'Classé sans suite' => 'bg-dark',
                                                default => 'bg-secondary'
                                            };
                                        ?>
                                        <span class="badge <?= $badgeStatut ?>"><?= htmlspecialchars($inc['statut']) ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="index.php?action=incidents_show&id=<?= $inc['id'] ?>" class="btn btn-sm btn-light border text-primary me-1" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?action=incidents_edit&id=<?= $inc['id'] ?>" class="btn btn-sm btn-light border text-warning me-1" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?action=incidents_delete&id=<?= $inc['id'] ?>" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet incident ?');" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fs-3 d-block mb-2"></i>
                                    Aucun incident enregistré pour le moment.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>