<?php
require_once __DIR__ . '/../models/Examen.php';
require_once __DIR__ . '/../models/Patient.php';

class ExamenController {
    private $examenModel;
    private $patientModel;

    public function __construct() {
        $this->examenModel = new Examen(); 
        $this->patientModel = new Patient();
    }

    // =========================================================================
    // GESTION DU MENU GLOBAL "EXAMENS"
    // =========================================================================

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $examens = $this->examenModel->getAllDemandes();
        require_once __DIR__ . '/../views/examens/index.php';
    }

    // Traiter la mise à jour globale (Résultats + Fichier joint automatique dans les pièces jointes du patient)
    public function updateGlobal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['examen_id'] ?? null;
            $statut = $_POST['statut'] ?? 'En attente';
            $resultats = trim($_POST['resultats'] ?? '');

            if ($id) {
                // 1. Mettre à jour l'examen global
                $this->examenModel->updateResultatsGlobal($id, $statut, $resultats);

                // 2. Récupérer les détails de l'examen pour connaître le patient et la consultation
                $examenInfo = $this->examenModel->getDemandeGlobaleById($id);

                if ($examenInfo) {
                    $filePathDb = '';

                    // 3. Gérer l'upload du fichier de résultats s'il est fourni
                    if (isset($_FILES['fichier_resultat']) && $_FILES['fichier_resultat']['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['fichier_resultat']['tmp_name'];
                        $fileName = $_FILES['fichier_resultat']['name'];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $uploadFileDir = __DIR__ . '/../../public/uploads/examens/';
                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0755, true);
                            }

                            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $filePathDb = 'uploads/examens/' . $newFileName;
                            }
                        }
                    }

                    // 4. Si un résultat ou un fichier a été soumis, enregistrer une copie dans les pièces jointes du patient
                    if (!empty($resultats) || !empty($filePathDb)) {
                        $titreExamen = (!empty($examenInfo['titre']) ? $examenInfo['titre'] : 'Examen') . ' (' . (!empty($examenInfo['type_examen']) ? $examenInfo['type_examen'] : 'Général') . ')';
                        
                        $this->examenModel->create([
                            'patient_id' => $examenInfo['patient_id'],
                            'consultation_id' => $examenInfo['consultation_id'],
                            'titre' => 'Résultat : ' . $titreExamen,
                            'type_examen' => $examenInfo['type_examen'] ?? 'Examen',
                            'date_examen' => date('Y-m-d'),
                            'resultats' => $resultats,
                            'compte_rendu' => 'Importé automatiquement depuis le module Examens.',
                            'fichier_path' => $filePathDb
                        ]);
                    }
                }
            }

            header('Location: index.php?action=examens');
            exit();
        }
    }

    // =========================================================================
    // MÉTHODES EXISTANTES (Dossier patient)
    // =========================================================================

    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $patient_id = $_GET['patient_id'] ?? null;
        if (!$patient_id) {
            header('Location: index.php?action=patients');
            exit();
        }

        $patient = $this->patientModel->getPatientById($patient_id);
        if (!$patient) {
            header('Location: index.php?action=patients');
            exit();
        }

        require_once __DIR__ . '/../views/examens/create.php';
    }

    public function store() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patient_id = $_POST['patient_id'] ?? null;
            if (!$patient_id) {
                header('Location: index.php?action=patients');
                exit();
            }

            $filePathDb = '';
            if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['fichier']['tmp_name'];
                $fileName = $_FILES['fichier']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadFileDir = __DIR__ . '/../../public/uploads/examens/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $filePathDb = 'uploads/examens/' . $newFileName;
                    }
                }
            }

            $data = [
                'patient_id' => $patient_id,
                'consultation_id' => !empty($_POST['consultation_id']) ? $_POST['consultation_id'] : null,
                'titre' => trim($_POST['titre'] ?? ''),
                'type_examen' => trim($_POST['type_examen'] ?? ''),
                'date_examen' => $_POST['date_examen'] ?? date('Y-m-d'),
                'resultats' => trim($_POST['resultats'] ?? ''),
                'compte_rendu' => trim($_POST['compte_rendu'] ?? ''),
                'fichier_path' => $filePathDb
            ];

            $this->examenModel->create($data);
            header('Location: index.php?action=patient_dossier&id=' . $patient_id);
            exit();
        }
    }

    public function edit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=patients');
            exit();
        }

        $examen = $this->examenModel->getById($id);
        if (!$examen) {
            header('Location: index.php?action=patients');
            exit();
        }

        $patient = $this->patientModel->getPatientById($examen['patient_id']);
        if (!$patient) {
            header('Location: index.php?action=patients');
            exit();
        }

        require_once __DIR__ . '/../views/examens/edit.php';
    }

    public function update() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $patient_id = $_POST['patient_id'] ?? null;

            if (!$id || !$patient_id) {
                header('Location: index.php?action=patients');
                exit();
            }

            $existingExamen = $this->examenModel->getById($id);
            $filePathDb = $existingExamen['fichier_path'] ?? '';

            if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['fichier']['tmp_name'];
                $fileName = $_FILES['fichier']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $uploadFileDir = __DIR__ . '/../../public/uploads/examens/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $dest_path = $uploadFileDir . $newFileName;
                    
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $filePathDb = 'uploads/examens/' . $newFileName;
                    }
                }
            }

            $data = [
                'titre' => propreTexte($_POST['titre'] ?? ''),
                'type_examen' => trim($_POST['type_examen'] ?? ''),
                'date_examen' => $_POST['date_examen'] ?? date('Y-m-d'),
                'resultats' => trim($_POST['resultats'] ?? ''),
                'compte_rendu' => trim($_POST['compte_rendu'] ?? ''),
                'fichier_path' => $filePathDb
            ];

            $this->examenModel->update($id, $data);
            header('Location: index.php?action=patient_dossier&id=' . $patient_id);
            exit();
        }
    }
}
?>