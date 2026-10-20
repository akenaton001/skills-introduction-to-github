<?php
require_once __DIR__ . '/../models/Medecin.php';

class MedecinController {
    private $medecinModel;

    public function __construct($db) {
        $this->medecinModel = new Medecin($db);
    }

    // Afficher la liste des médecins
    public function index() {
        $medecinsList = $this->medecinModel->getAllMedecins();
        require_once __DIR__ . '/../views/medecins/index.php';
    }

    // Gérer l'ajout d'un médecin
    public function creer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim($_POST['nom']),
                'prenom' => trim($_POST['prenom']),
                'email' => trim($_POST['email']),
                'mot_de_passe' => $_POST['mot_de_passe']
            ];

            if ($this->medecinModel->createMedecin($data)) {
                header('Location: index.php?action=medecins_liste&success=1');
                exit();
            }
        }
        require_once __DIR__ . '/../views/medecins/create.php';
    }

    // Gérer la modification d'un médecin
    public function modifier() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=medecins_liste');
            exit();
        }

        $medecin = $this->medecinModel->getMedecinById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom' => trim($_POST['nom']),
                'prenom' => trim($_POST['prenom']),
                'email' => trim($_POST['email']),
                'mot_de_passe' => $_POST['mot_de_passe'] ?? ''
            ];

            if ($this->medecinModel->updateMedecin($id, $data)) {
                header('Location: index.php?action=medecins_liste&update=1');
                exit();
            }
        }

        require_once __DIR__ . '/../views/medecins/edit.php';
    }

    // Gérer la suppression d'un médecin
    public function supprimer() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->medecinModel->deleteMedecin($id);
        }
        header('Location: index.php?action=medecins_liste&delete=1');
        exit();
    }
}