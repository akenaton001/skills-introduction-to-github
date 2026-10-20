<?php
require_once __DIR__ . '/../models/Hospitalisation.php';
require_once __DIR__ . '/../models/Patient.php';

class HospitalisationController {
    private $hospitalisationModel;
    private $patientModel;

    public function __construct() {
        $this->hospitalisationModel = new Hospitalisation();
        $this->patientModel = new Patient();
    }

    // Afficher la liste de toutes les hospitalisations
    public function index() {
        $hospitalisations = $this->hospitalisationModel->getAll();
        require_once __DIR__ . '/../views/hospitalisations/index.php';
    }

    // Afficher le formulaire d'admission
    public function create() {
        $patient_id = $_GET['patient_id'] ?? null;
        $patients = $this->patientModel->getAllPatients(); // Pour choisir le patient si non spécifié
        $patient = $patient_id ? $this->patientModel->getPatientById($patient_id) : null;
        
        require_once __DIR__ . '/../views/hospitalisations/create.php';
    }

    // Enregistrer l'admission en POST
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'patient_id' => $_POST['patient_id'],
                'numero_chambre' => $_POST['numero_chambre'],
                'numero_lit' => $_POST['numero_lit'],
                'date_entree' => $_POST['date_entree'] ?? date('Y-m-d H:i:s'),
                'motif_hospitalisation' => $_POST['motif_hospitalisation']
            ];

            $this->hospitalisationModel->create($data);
            header('Location: index.php?action=hospitalisations');
            exit;
        }
    }

    // Marquer la sortie du patient (Libérer le lit)
    public function discharge() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->hospitalisationModel->discharge($id, date('Y-m-d H:i:s'));
        }
        header('Location: index.php?action=hospitalisations');
        exit;
    }
}