<?php
require_once __DIR__ . '/../models/Patient.php';

class PatientController {

    // Afficher la liste de tous les patients
    public function index() {
        // Sécurité : Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $patientModel = new Patient();
        $patients = $patientModel->getAllPatients();

        // Charger la vue de la liste des patients
        require_once __DIR__ . '/../views/patients/index.php';
    }

    // Afficher le formulaire d'ajout d'un nouveau patient
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        require_once __DIR__ . '/../views/patients/create.php';
    }

    // Traiter l'enregistrement d'un nouveau patient (POST)
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientModel = new Patient();

            // Génération du code patient basé sur la date du jour et le nombre d'inscriptions (ex: 23092026/001)
            $dateJourFormatee = date('dmY'); // 23092026
            $dateJourSql = date('Y-m-d');
            
            // Compter combien de patients ont été enregistrés aujourd'hui pour faire l'incrément sur 3 chiffres
            $countToday = method_exists($patientModel, 'countPatientsByDate') ? $patientModel->countPatientsByDate($dateJourSql) : 0;
            $numeroOrdre = str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
            $codePatient = $dateJourFormatee . '/' . $numeroOrdre;

            $data = [
                'code_patient' => $codePatient,
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'date_naissance' => $_POST['date_naissance'] ?? '',
                'sexe' => $_POST['sexe'] ?? '',
                'telephone' => trim($_POST['telephone'] ?? ''),
                'adresse' => trim($_POST['adresse'] ?? ''),
                'personne_prevenir' => trim($_POST['personne_prevenir'] ?? ''),
                'telephone_prevenir' => trim($_POST['telephone_prevenir'] ?? ''),
                'groupe_sanguin' => trim($_POST['groupe_sanguin'] ?? ''),
                'antecedents' => trim($_POST['antecedents'] ?? '')
            ];

            if (!empty($data['nom']) && !empty($data['prenom']) && !empty($data['date_naissance']) && !empty($data['telephone'])) {
                $patientModel->createPatient($data);

                header('Location: index.php?action=patients');
                exit();
            } else {
                $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
                header('Location: index.php?action=patients_nouveau');
                exit();
            }
        }
    }

    // Afficher le formulaire de modification d'un patient
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=patients');
            exit();
        }

        $patientModel = new Patient();
        $patient = $patientModel->getPatientById((int)$id);

        if (!$patient) {
            header('Location: index.php?action=patients');
            exit();
        }

        require_once __DIR__ . '/../views/patients/edit.php';
    }

    // Traiter la modification d'un patient (POST)
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: index.php?action=patients');
                exit();
            }

            $data = [
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'date_naissance' => $_POST['date_naissance'] ?? '',
                'sexe' => $_POST['sexe'] ?? '',
                'telephone' => trim($_POST['telephone'] ?? ''),
                'adresse' => trim($_POST['adresse'] ?? ''),
                'personne_prevenir' => trim($_POST['personne_prevenir'] ?? ''),
                'telephone_prevenir' => trim($_POST['telephone_prevenir'] ?? ''),
                'groupe_sanguin' => trim($_POST['groupe_sanguin'] ?? ''),
                'antecedents' => trim($_POST['antecedents'] ?? '')
            ];

            if (!empty($data['nom']) && !empty($data['prenom']) && !empty($data['date_naissance']) && !empty($data['telephone'])) {
                $patientModel = new Patient();
                $patientModel->updatePatient((int)$id, $data);

                // Rediriger vers le dossier unique du patient après modification
                header('Location: index.php?action=patient_dossier&id=' . $id);
                exit();
            } else {
                $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
                header('Location: index.php?action=patient_edit&id=' . $id);
                exit();
            }
        }
    }

    // Afficher le dossier complet et unique d'un patient
    public function show() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=patients');
            exit();
        }

        $patientModel = new Patient();
        
        // Récupérer les informations du patient
        $patient = $patientModel->getPatientById((int)$id);
        if (!$patient) {
            header('Location: index.php?action=patients');
            exit();
        }

        // Récupérer les données associées pour le dossier unique
        $consultations = $patientModel->getConsultationsByPatientId((int)$id);
        $examens = $patientModel->getExamensByPatientId((int)$id);
        $hospitalisation = $patientModel->getActiveHospitalisationByPatientId((int)$id);

        // Charger la vue détaillée
        require_once __DIR__ . '/../views/patients/show.php';
    }

    // Traiter la recherche globale (par nom, code, téléphone, etc.)
    public function rechercher() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $terme = trim($_GET['q'] ?? '');
        $patients = [];

        if (!empty($terme)) {
            $patientModel = new Patient();
            // Assure-toi que cette méthode existe dans ton modèle Patient (voir détail ci-dessous)
            $patients = $patientModel->rechercherParMotCle($terme);
        }

        // Charger la vue des résultats de recherche
        require_once __DIR__ . '/../views/patients/recherche.php';
    }
}