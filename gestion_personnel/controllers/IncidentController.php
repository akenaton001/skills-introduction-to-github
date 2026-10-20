<?php
require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../config/database.php';

class IncidentController {
    private $incidentModel;
    private $employeeModel;

    public function __construct() {
        $database = new Database();
        $pdo = $database->getConnection();

        $this->incidentModel = new Incident($pdo);
        $this->employeeModel = new Employee($pdo);
    }

    public function index() {
        $incidents = $this->incidentModel->getAll();
        require_once __DIR__ . '/../views/incidents/index.php';
    }

    public function show() {
        $id = $_GET['id'] ?? null;
        $incident = $this->incidentModel->getById($id);
        if (!$incident) {
            header('Location: index.php?action=incidents');
            exit;
        }
        require_once __DIR__ . '/../views/incidents/show.php';
    }

    public function create() {
        $employees = $this->employeeModel->getAll();
        require_once __DIR__ . '/../views/incidents/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $uploadedFiles = $this->handleUploads();

            // Renseigner les deux champs pour assurer la compatibilité BDD et vue
            $jsonFiles = !empty($uploadedFiles) ? json_encode($uploadedFiles) : null;
            $_POST['fichiers_joints'] = $jsonFiles;
            $_POST['fichier_joint'] = $jsonFiles;
            
            $this->incidentModel->create($_POST);
            header('Location: index.php?action=incidents');
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $incident = $this->incidentModel->getById($id);
        $employees = $this->employeeModel->getAll();
        require_once __DIR__ . '/../views/incidents/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                header('Location: index.php?action=incidents');
                exit;
            }

            // 1. Récupérer l'enregistrement existant en base de données
            $currentIncident = $this->incidentModel->getById($id);
            if (!$currentIncident) {
                header('Location: index.php?action=incidents');
                exit;
            }

            // 2. Traitement et conservation des pièces jointes
            $existingData = $currentIncident['fichiers_joints'] ?? $currentIncident['fichier_joint'] ?? null;
            $existingFiles = $this->extractFilesArray($existingData);
            $newFiles = $this->handleUploads();
            $allFiles = array_merge($existingFiles, $newFiles);

            $jsonFiles = !empty($allFiles) ? json_encode(array_values($allFiles)) : null;

            // 3. Fusionner les données existantes avec les données reçues de $_POST
            // Empêche l'écrasement par des valeurs NULL lors de l'évolution rapide (dénouement/statut)
            $payload = array_merge($currentIncident, $_POST);

            // Garantir que les clés des fichiers restent synchronisées
            if ($jsonFiles !== null) {
                $payload['fichiers_joints'] = $jsonFiles;
                $payload['fichier_joint'] = $jsonFiles;
            }

            // 4. Exécuter la mise à jour
            $this->incidentModel->update($id, $payload);

            // 5. Redirection vers la vue détaillée avec l'action 'incidents_show'
            header("Location: index.php?action=incidents_show&id={$id}");
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $incident = $this->incidentModel->getById($id);
            if ($incident) {
                $rawFiles = $incident['fichiers_joints'] ?? $incident['fichier_joint'] ?? null;
                $filesToDelete = $this->extractFilesArray($rawFiles);
                $this->removePhysicalFiles($filesToDelete);
                $this->incidentModel->delete($id);
            }
        }
        header('Location: index.php?action=incidents');
        exit;
    }

    /**
     * Gère l'upload de un ou plusieurs fichiers depuis les inputs `fichiers_joints[]` ou `fichier_joint`
     */
    private function handleUploads(): array {
        $uploadedNames = [];
        
        $fileKey = null;
        if (isset($_FILES['fichiers_joints'])) {
            $fileKey = 'fichiers_joints';
        } elseif (isset($_FILES['fichier_joint'])) {
            $fileKey = 'fichier_joint';
        }

        if (!$fileKey) {
            return $uploadedNames;
        }

        $uploadDir = __DIR__ . '/../uploads/incidents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $files = $_FILES[$fileKey];

        if (is_array($files['name'])) {
            foreach ($files['name'] as $index => $name) {
                if ($files['error'][$index] === UPLOAD_ERR_OK && !empty($files['tmp_name'][$index])) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    $filename = 'incident_' . time() . '_' . uniqid() . '_' . $index . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$index], $uploadDir . $filename)) {
                        $uploadedNames[] = $filename;
                    }
                }
            }
        } else {
            if ($files['error'] === UPLOAD_ERR_OK && !empty($files['tmp_name'])) {
                $ext = strtolower(pathinfo($files['name'], PATHINFO_EXTENSION));
                $filename = 'incident_' . time() . '_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($files['tmp_name'], $uploadDir . $filename)) {
                    $uploadedNames[] = $filename;
                }
            }
        }

        return $uploadedNames;
    }

    /**
     * Extrait un tableau de fichiers à partir d'une chaîne BDD (JSON ou texte séparé par des virgules)
     */
    private function extractFilesArray($rawData): array {
        if (empty($rawData)) {
            return [];
        }
        $decoded = json_decode($rawData, true);
        if (is_array($decoded)) {
            return $decoded;
        }
        return array_filter(array_map('trim', explode(',', $rawData)));
    }

    /**
     * Supprime physiquement les fichiers du disque
     */
    private function removePhysicalFiles(array $filenames): void {
        $uploadDir = __DIR__ . '/../uploads/incidents/';
        foreach ($filenames as $file) {
            $filePath = $uploadDir . $file;
            if (!empty($file) && file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}