<?php
require_once __DIR__ . '/../models/Dispute.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class DisputeController {
    private $disputeModel;
    private $employeeModel;

    public function __construct() {
        // Initialisation de la connexion PDO avec la classe Database
        $database = new Database();
        $pdo = $database->getConnection();

        $this->disputeModel = new Dispute($pdo);
        $this->employeeModel = new Employee($pdo);
    }

    // Liste de tous les contentieux (avec gestion des filtres et de la recherche)
    public function index() {
        $filters = [
            'statut'  => $_GET['statut'] ?? '',
            'gravite' => $_GET['gravite'] ?? '',
            'type'    => $_GET['type'] ?? '',
            'search'  => $_GET['search'] ?? ''
        ];

        $disputes = $this->disputeModel->getAll($filters);
        require_once __DIR__ . '/../views/disputes/index.php';
    }

    // Affichage détaillé d'un dossier de contentieux avec son historique d'évolutions
    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Identifiant de dossier manquant.'];
            header('Location: index.php?action=disputes');
            exit;
        }

        $dispute = $this->disputeModel->getById($id);

        if (!$dispute) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Dossier introuvable.'];
            header('Location: index.php?action=disputes');
            exit;
        }

        // Récupération de l'historique chronologique des dénouements
        $updates = $this->disputeModel->getUpdatesByDisputeId($id);

        require_once __DIR__ . '/../views/disputes/show.php';
    }

    // Formulaire de création
    public function create() {
        $employees = $this->employeeModel->getAll();
        require_once __DIR__ . '/../views/disputes/create.php';
    }

    // Formulaire d'édition du dossier
    public function edit() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Identifiant de dossier manquant.'];
            header('Location: index.php?action=disputes');
            exit;
        }

        $dispute = $this->disputeModel->getById($id);

        if (!$dispute) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Dossier introuvable.'];
            header('Location: index.php?action=disputes');
            exit;
        }

        $employees = $this->employeeModel->getAll();
        require_once __DIR__ . '/../views/disputes/edit.php';
    }

    // Enregistrer un nouveau contentieux
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileName = null;

            // Gestion de l'upload de fichier principal du dossier
            if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/disputes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $extension = strtolower(pathinfo($_FILES['fichier_joint']['name'], PATHINFO_EXTENSION));
                $fileName = 'contentieux_' . time() . '_' . uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['fichier_joint']['tmp_name'], $uploadDir . $fileName);
            }

            $data = [
                'employee_id'        => !empty($_POST['employee_id']) ? $_POST['employee_id'] : null,
                'type'               => $_POST['type'] ?? 'Interne',
                'categorie'          => $_POST['categorie'] ?? '',
                'gravite'            => $_POST['gravite'] ?? 'Moyen',
                'objet'              => $_POST['objet'] ?? '',
                'description'        => $_POST['description'] ?? null,
                'tiers_impliques'    => $_POST['tiers_impliques'] ?? null,
                'date_ouverture'     => $_POST['date_ouverture'] ?? date('Y-m-d'),
                'date_limite_reponse'=> !empty($_POST['date_limite_reponse']) ? $_POST['date_limite_reponse'] : null,
                'statut'             => $_POST['statut'] ?? 'En cours',
                'decision'           => $_POST['decision'] ?? null,
                'impact_financier'   => !empty($_POST['impact_financier']) ? $_POST['impact_financier'] : 0,
                'fichier_joint'      => $fileName
            ];

            if ($this->disputeModel->create($data)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Dossier de contentieux enregistré avec succès.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la création du dossier.'];
            }

            header('Location: index.php?action=disputes');
            exit;
        }
    }

    // Traiter la mise à jour globale du dossier
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Dossier introuvable.'];
                header('Location: index.php?action=disputes');
                exit;
            }

            $dispute = $this->disputeModel->getById($id);
            $fileName = $dispute['fichier_joint'];

            // Gestion de l'upload du nouveau fichier si transmis
            if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/disputes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                if (!empty($fileName) && file_exists($uploadDir . $fileName)) {
                    @unlink($uploadDir . $fileName);
                }

                $extension = strtolower(pathinfo($_FILES['fichier_joint']['name'], PATHINFO_EXTENSION));
                $fileName = 'contentieux_' . time() . '_' . uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['fichier_joint']['tmp_name'], $uploadDir . $fileName);
            }

            $data = [
                'employee_id'         => !empty($_POST['employee_id']) ? $_POST['employee_id'] : null,
                'type'                => $_POST['type'] ?? 'Interne',
                'categorie'           => $_POST['categorie'] ?? '',
                'gravite'             => $_POST['gravite'] ?? 'Moyen',
                'objet'               => $_POST['objet'] ?? '',
                'description'         => $_POST['description'] ?? null,
                'tiers_impliques'     => $_POST['tiers_impliques'] ?? null,
                'date_ouverture'      => $_POST['date_ouverture'] ?? date('Y-m-d'),
                'date_limite_reponse' => !empty($_POST['date_limite_reponse']) ? $_POST['date_limite_reponse'] : null,
                'date_cloture'        => !empty($_POST['date_cloture']) ? $_POST['date_cloture'] : null,
                'statut'              => $_POST['statut'] ?? $dispute['statut'],
                'decision'            => $_POST['decision'] ?? $dispute['decision'],
                'impact_financier'    => !empty($_POST['impact_financier']) ? $_POST['impact_financier'] : 0,
                'fichier_joint'       => $fileName
            ];

            if ($this->disputeModel->update($id, $data)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Dossier mis à jour avec succès.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de la modification du dossier.'];
            }

            header('Location: index.php?action=disputes_show&id=' . $id);
            exit;
        }
    }

    // Ajouter un nouveau dénouement / étape à l'historique
    public function updateDecision() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Dossier introuvable.'];
                header('Location: index.php?action=disputes');
                exit;
            }

            $fileName = null;

            // Gestion de l'upload de la pièce jointe propre à cette étape d'évolution
            if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/disputes/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $extension = strtolower(pathinfo($_FILES['fichier_joint']['name'], PATHINFO_EXTENSION));
                $fileName = 'update_' . time() . '_' . uniqid() . '.' . $extension;
                move_uploaded_file($_FILES['fichier_joint']['tmp_name'], $uploadDir . $fileName);
            }

            $statut = $_POST['statut'] ?? 'En cours';
            $decision = $_POST['decision'] ?? '';

            if ($this->disputeModel->addUpdate($id, $statut, $decision, $fileName)) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => 'Nouvelle évolution enregistrée avec succès.'];
            } else {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors de l\'enregistrement de l\'évolution.'];
            }

            header('Location: index.php?action=disputes_show&id=' . $id);
            exit;
        }
    }

    // Mettre à jour uniquement le document principal joint
    public function updateDocument() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Dossier introuvable.'];
                header('Location: index.php?action=disputes');
                exit;
            }

            if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] === UPLOAD_ERR_OK) {
                $dispute = $this->disputeModel->getById($id);
                $uploadDir = __DIR__ . '/../uploads/disputes/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Supprimer l'ancien fichier principal s'il existe
                if (!empty($dispute['fichier_joint']) && file_exists($uploadDir . $dispute['fichier_joint'])) {
                    @unlink($uploadDir . $dispute['fichier_joint']);
                }

                $extension = strtolower(pathinfo($_FILES['fichier_joint']['name'], PATHINFO_EXTENSION));
                $fileName = 'contentieux_' . time() . '_' . uniqid() . '.' . $extension;

                if (move_uploaded_file($_FILES['fichier_joint']['tmp_name'], $uploadDir . $fileName)) {
                    $this->disputeModel->updateDocument($id, $fileName);
                    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Document mis à jour avec succès.'];
                } else {
                    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Erreur lors du téléversement du fichier.'];
                }
            }

            header('Location: index.php?action=disputes_show&id=' . $id);
            exit;
        }
    }

    // Supprimer un contentieux
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $dispute = $this->disputeModel->getById($id);
            
            // Suppression du fichier principal
            if ($dispute && !empty($dispute['fichier_joint'])) {
                $filePath = __DIR__ . '/../uploads/disputes/' . $dispute['fichier_joint'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            // Suppression des fichiers de l'historique
            $updates = $this->disputeModel->getUpdatesByDisputeId($id);
            foreach ($updates as $up) {
                if (!empty($up['fichier_joint'])) {
                    $upFilePath = __DIR__ . '/../uploads/disputes/' . $up['fichier_joint'];
                    if (file_exists($upFilePath)) {
                        @unlink($upFilePath);
                    }
                }
            }

            $this->disputeModel->delete($id);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Contentieux et son historique supprimés avec succès.'];
        }
        header('Location: index.php?action=disputes');
        exit;
    }
}