<?php
require_once __DIR__ . '/../models/RendezVousModel.php';

class RendezVousController {
    private $model;

    public function __construct() {
        // Connexion à la base de données
        require_once __DIR__ . '/../config/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        $this->model = new RendezVousModel($db);
    }

    // Afficher la liste des rendez-vous
    public function index() {
        $rendezVousList = $this->model->tousLesRendezVous();
        require_once __DIR__ . '/../views/rendez_vous/index.php';
    }

    // Afficher le formulaire de création de rendez-vous
    public function creer() {
        $patients = $this->model->getTousLesPatients();
        $medecins = $this->model->getMedecins();
        require_once __DIR__ . '/../views/rendez_vous/creer.php';
    }

    // Traiter l'enregistrement du formulaire (Création)
    public function enregistrer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'medecin_id' => $_POST['medecin_id'] ?? null,
                'date_heure_rdv' => $_POST['date_heure_rdv'] ?? null,
                'statut' => $_POST['statut'] ?? 'planifie',
                'notes' => $_POST['notes'] ?? null
            ];

            if ($this->model->insererRendezVous($data)) {
                header('Location: index.php?action=rendez_vous&success=1');
                exit();
            } else {
                header('Location: index.php?action=rendez_vous_creer&error=1');
                exit();
            }
        }
    }

    // --- ACTIONS DU MENU DÉROULANT (CHANGER STATUT & SUPPRIMER) ---

    // Changer rapidement le statut (ex: Confirmer, Annuler en un clic)
    public function changerStatut() {
        $id = $_GET['id'] ?? null;
        $statut = $_GET['statut'] ?? null;

        if ($id && $statut) {
            // Appel à la méthode du modèle (assure-toi qu'elle existe dans RendezVousModel)
            if (method_exists($this->model, 'updateStatut')) {
                $this->model->updateStatut($id, $statut);
            }
        }
        header('Location: index.php?action=rendez_vous');
        exit();
    }

    // Supprimer un rendez-vous
    public function supprimer() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            if (method_exists($this->model, 'supprimerRendezVous')) {
                $this->model->supprimerRendezVous($id);
            } elseif (method_exists($this->model, 'delete')) {
                $this->model->delete($id);
            }
        }
        header('Location: index.php?action=rendez_vous');
        exit();
    }

    // --- GESTION DE LA MODIFICATION ---

    // Afficher le formulaire de modification d'un rendez-vous existant
    public function modifier() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=rendez_vous');
            exit();
        }

        $rendezVous = $this->model->getRendezVousById($id);
        if (!$rendezVous) {
            header('Location: index.php?action=rendez_vous');
            exit();
        }

        $patients = $this->model->getTousLesPatients();
        $medecins = $this->model->getMedecins();

        require_once __DIR__ . '/../views/rendez_vous/modifier.php';
    }

    // Traiter la mise à jour (Recalibrage de la date, statut, notes par le secrétariat)
    public function mettreAJour() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: index.php?action=rendez_vous');
                exit();
            }

            $data = [
                'patient_id' => $_POST['patient_id'] ?? null,
                'medecin_id' => $_POST['medecin_id'] ?? null,
                'date_heure_rdv' => $_POST['date_heure_rdv'] ?? null,
                'statut' => $_POST['statut'] ?? 'planifie',
                'notes' => $_POST['notes'] ?? null
            ];

            if ($this->model->updateRendezVous($id, $data)) {
                header('Location: index.php?action=rendez_vous&success=update');
                exit();
            } else {
                header('Location: index.php?action=rendez_vous_modifier&id=' . $id . '&error=1');
                exit();
            }
        }
    }
}
?>