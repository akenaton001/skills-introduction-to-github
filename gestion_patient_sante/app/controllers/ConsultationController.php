<?php
require_once __DIR__ . '/../models/Consultation.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/RendezVousModel.php'; // Correction du nom du fichier modèle
require_once __DIR__ . '/../models/Examen.php'; // Importation du modèle Examen

class ConsultationController {
    private $consultationModel;
    private $patientModel;
    private $rendezVousModel;
    private $examenModel; // Déclaration de la propriété examenModel

    public function __construct() {
        $this->consultationModel = new Consultation();
        $this->patientModel = new Patient();
        $this->rendezVousModel = new RendezVousModel(); // Correction du nom de la classe instanciée
        $this->examenModel = new Examen(); // Initialisation du modèle Examen
    }

    // Afficher la liste de toutes les consultations et rendez-vous unifiés
    public function index() {
        // Récupérer le filtre de date depuis l'URL (si présent)
        $dateFilter = $_GET['date_filtre'] ?? null;

        // Récupérer à la fois les consultations et les rendez-vous via la nouvelle méthode unifiée
        $tousElements = $this->consultationModel->getAllConsultationsEtRendezVous($dateFilter);

        // Pour conserver la compatibilité avec ta vue actuelle qui utilise $consultations, 
        // on lui passe la liste complète unifiée.
        $consultations = $tousElements;

        // Charger la vue de la liste des consultations
        require_once __DIR__ . '/../views/consultations/index.php';
    }

    public function create() {
        $patient_id = $_GET['patient_id'] ?? null;
        if (!$patient_id) {
            header('Location: index.php?action=patients');
            exit;
        }

        $patient = $this->patientModel->getPatientById($patient_id);
        if (!$patient) {
            header('Location: index.php?action=patients');
            exit;
        }

        require_once __DIR__ . '/../views/consultations/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $medecin_id = $_SESSION['user_id'] ?? 1;

            $data = [
                'patient_id' => $_POST['patient_id'],
                'medecin_id' => $medecin_id,
                'date_consultation' => $_POST['date_consultation'] ?? date('Y-m-d H:i:s'),
                'evolution' => $_POST['evolution'], 
                'motif' => $_POST['motif'],
                'symptomes' => $_POST['symptomes'] ?? '',
                'diagnostic' => $_POST['diagnostic'],
                'prescription' => $_POST['prescription'] ?? '', 
                'examens_prescrits' => $_POST['examens_prescrits'] ?? '',
                'date_prochain_rv' => !empty($_POST['date_prochain_rv']) ? $_POST['date_prochain_rv'] : null
            ];

            // 1. Enregistrer la consultation
            $consultation_id = $this->consultationModel->create($data);

            // 1.2. AUTOMATISATION EXAMEN : Si le médecin a prescrit un examen textuel, on l'ajoute dans le tableau global des examens
            if (!empty($data['examens_prescrits'])) {
                $this->examenModel->createDemandeGlobale([
                    'patient_id' => $data['patient_id'],
                    'consultation_id' => $consultation_id,
                    'titre' => $data['examens_prescrits'],
                    'type_examen' => 'Prescription Médicale',
                    'date_prescription' => $data['date_consultation'],
                    'statut' => 'En attente',
                    'medecin_prescripteur' => 'Dr. ID ' . $medecin_id
                ]);
            }

            // 1.5. AUTOMATISATION : Si le médecin a fixé un prochain RV, on l'ajoute dans l'agenda
            if (!empty($data['date_prochain_rv'])) {
                $this->rendezVousModel->insererRendezVous([
                    'patient_id' => $data['patient_id'],
                    'medecin_id' => $medecin_id,
                    'date_heure_rdv' => $data['date_prochain_rv'],
                    'statut' => 'planifie',
                    'notes' => 'Prochain rendez-vous proposé suite à la consultation du ' . date('d/m/Y')
                ]);
            }

            // 2. Traitement des fichiers multiples
            if (isset($_FILES['fichiers_examens']) && !empty($_FILES['fichiers_examens']['name'][0])) {
                $fileCount = count($_FILES['fichiers_examens']['name']);
                $uploadFileDir = __DIR__ . '/../../public/uploads/examens/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['fichiers_examens']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['fichiers_examens']['tmp_name'][$i];
                        $fileName = $_FILES['fichiers_examens']['name'][$i];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFileName = md5(time() . $fileName . $i) . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $filePathDb = 'uploads/examens/' . $newFileName;

                                $this->consultationModel->addItemPieceJointe([
                                    'patient_id' => $_POST['patient_id'],
                                    'consultation_id' => $consultation_id,
                                    'titre' => $fileName,
                                    'type_examen' => $_POST['type_examen'] ?? 'autre',
                                    'fichier_path' => $filePathDb
                                ]);
                            }
                        }
                    }
                }
            }

            header('Location: index.php?action=patient_dossier&id=' . $_POST['patient_id']);
            exit;
        }
    }

    // Afficher le formulaire de modification d'une consultation
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=patients');
            exit;
        }

        // Récupérer la consultation à modifier
        $consultation = $this->consultationModel->getById($id);
        if (!$consultation) {
            header('Location: index.php?action=patients');
            exit;
        }

        // Récupérer les informations du patient associé
        $patient = $this->patientModel->getPatientById($consultation['patient_id']);
        if (!$patient) {
            header('Location: index.php?action=patients');
            exit;
        }

        require_once __DIR__ . '/../views/consultations/edit.php';
    }

    // Traiter la modification d'une consultation (POST)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $patient_id = $_POST['patient_id'] ?? null;

            if (!$id || !$patient_id) {
                header('Location: index.php?action=patients');
                exit;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $medecin_id = $_SESSION['user_id'] ?? 1;

            $data = [
                'date_consultation' => $_POST['date_consultation'] ?? date('Y-m-d H:i:s'),
                'evolution' => $_POST['evolution'] ?? '',
                'motif' => $_POST['motif'] ?? '',
                'symptomes' => $_POST['symptomes'] ?? '',
                'diagnostic' => $_POST['diagnostic'] ?? '',
                'prescription' => $_POST['prescription'] ?? '',
                'examens_prescrits' => $_POST['examens_prescrits'] ?? '',
                'date_prochain_rv' => !empty($_POST['date_prochain_rv']) ? $_POST['date_prochain_rv'] : null
            ];

            // 1. Mettre à jour les données de la consultation dans le modèle
            $this->consultationModel->update($id, $data);

            // 1.5. AUTOMATISATION : Si une date de prochain RV est renseignée lors de la modification
            if (!empty($data['date_prochain_rv'])) {
                $this->rendezVousModel->insererRendezVous([
                    'patient_id' => $patient_id,
                    'medecin_id' => $medecin_id,
                    'date_heure_rdv' => $data['date_prochain_rv'],
                    'statut' => 'planifie',
                    'notes' => 'Mise à jour du prochain rendez-vous (Consultation ID: ' . $id . ')'
                ]);
            }

            // 2. Traitement des nouveaux fichiers éventuels (pièces jointes supplémentaires)
            if (isset($_FILES['fichiers_examens']) && !empty($_FILES['fichiers_examens']['name'][0])) {
                $fileCount = count($_FILES['fichiers_examens']['name']);
                $uploadFileDir = __DIR__ . '/../../public/uploads/examens/';
                
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['fichiers_examens']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES['fichiers_examens']['tmp_name'][$i];
                        $fileName = $_FILES['fichiers_examens']['name'][$i];
                        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $newFileName = md5(time() . $fileName . $i) . '.' . $fileExtension;
                            $dest_path = $uploadFileDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $filePathDb = 'uploads/examens/' . $newFileName;

                                $this->consultationModel->addItemPieceJointe([
                                    'patient_id' => $patient_id,
                                    'consultation_id' => $id,
                                    'titre' => $fileName,
                                    'type_examen' => $_POST['type_examen'] ?? 'autre',
                                    'fichier_path' => $filePathDb
                                ]);
                            }
                        }
                    }
                }
            }

            // Rediriger vers le dossier unique du patient après modification
            header('Location: index.php?action=patient_dossier&id=' . $patient_id);
            exit;
        }
    }
}