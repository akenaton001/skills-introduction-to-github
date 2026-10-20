<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Employee.php';

class EmployeeController {
    private $db;
    private $employee;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->employee = new Employee($this->db);
    }

    public function index() {
        // Récupération des paramètres de filtrage depuis l'URL (GET)
        $search = $_GET['search'] ?? null;
        $contrat = $_GET['contrat'] ?? null;
        $poste = $_GET['poste'] ?? null;

        // Récupération des employés filtrés et des postes enregistrés
        $employees = $this->employee->search($search, $contrat, $poste);
        $postes = $this->employee->getPostes();

        require_once __DIR__ . '/../views/employees/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdfFileName = null;

            // Traitement de l'upload PDF
            if (isset($_FILES['contrat_pdf']) && $_FILES['contrat_pdf']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['contrat_pdf']['tmp_name'];
                $fileName = $_FILES['contrat_pdf']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    $newFileName = time() . '_' . uniqid() . '.pdf';
                    $uploadFileDir = __DIR__ . '/../uploads/contracts/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        $pdfFileName = $newFileName;
                    }
                }
            }

            // Génération automatique du matricule sur 6 chiffres (ex: 000001)
            $nextId = $this->employee->getNextAutoIncrementId();
            $matricule = str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // Préparation du tableau des données
            $data = [
                'matricule'              => $matricule,
                'entreprise'             => $_POST['entreprise'] ?? 'STC',
                'nom'                    => $_POST['nom'],
                'prenom'                 => $_POST['prenom'],
                'etat_civil'             => $_POST['etat_civil'] ?: null,
                'adresse'                => $_POST['adresse'] ?: null,
                'telephone'              => $_POST['telephone'] ?: null,
                'cni'                    => $_POST['cni'] ?: null,
                'email'                  => $_POST['email'] ?: null,
                'intitule_poste'         => $_POST['intitule_poste'] ?: null,
                'type_contrat'           => $_POST['type_contrat'],
                'date_embauche'          => $_POST['date_embauche'] ?: null,
                'numero_permis'          => $_POST['numero_permis'] ?: null,
                'categories_permis'      => $_POST['categories_permis'] ?: null,
                'date_expiration_permis' => $_POST['date_expiration_permis'] ?: null,
                'contrat_pdf'            => $pdfFileName
            ];

            // Insertion et gestion des messages de retour
            try {
                if ($this->employee->create($data)) {
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'L\'employé a été enregistré avec succès (Matricule : ' . $matricule . ') !'
                    ];
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Erreur lors de l\'enregistrement de l\'employé.'
                    ];
                }
            } catch (PDOException $e) {
                // Gestion propre des erreurs de doublons (CNI, email)
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Erreur : Un employé avec cette CNI ou cet e-mail existe déjà.'
                ];
            }
        }

        require_once __DIR__ . '/../views/employees/create.php';
    }

    public function edit() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php");
            exit;
        }

        // Récupération de l'employé à modifier
        $employee = $this->employee->getById($id);

        if (!$employee) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Employé introuvable.'
            ];
            header("Location: index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Par défaut, garder le fichier existant
            $pdfFileName = $_POST['existing_contrat_pdf'] ?? $employee['contrat_pdf'];

            // Traitement d'un nouveau fichier PDF si téléversé
            if (isset($_FILES['contrat_pdf']) && $_FILES['contrat_pdf']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['contrat_pdf']['tmp_name'];
                $fileName = $_FILES['contrat_pdf']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    $newFileName = time() . '_' . uniqid() . '.pdf';
                    $uploadFileDir = __DIR__ . '/../uploads/contracts/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                        // Supprimer l'ancien fichier du serveur si présent
                        if (!empty($employee['contrat_pdf']) && file_exists($uploadFileDir . $employee['contrat_pdf'])) {
                            unlink($uploadFileDir . $employee['contrat_pdf']);
                        }
                        $pdfFileName = $newFileName;
                    }
                }
            }

            // Préparation des données mises à jour (le matricule reste inchangé lors de l'édition)
            $data = [
                'entreprise'             => $_POST['entreprise'] ?? $employee['entreprise'],
                'nom'                    => $_POST['nom'],
                'prenom'                 => $_POST['prenom'],
                'etat_civil'             => $_POST['etat_civil'] ?: null,
                'adresse'                => $_POST['adresse'] ?: null,
                'telephone'              => $_POST['telephone'] ?: null,
                'cni'                    => $_POST['cni'] ?: null,
                'email'                  => $_POST['email'] ?: null,
                'intitule_poste'         => $_POST['intitule_poste'] ?: null,
                'type_contrat'           => $_POST['type_contrat'],
                'date_embauche'          => $_POST['date_embauche'] ?: null,
                'numero_permis'          => $_POST['numero_permis'] ?: null,
                'categories_permis'      => $_POST['categories_permis'] ?: null,
                'date_expiration_permis' => $_POST['date_expiration_permis'] ?: null,
                'contrat_pdf'            => $pdfFileName
            ];

            try {
                if ($this->employee->update($id, $data)) {
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'Les informations de l\'employé ont été mises à jour avec succès !'
                    ];
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Erreur lors de la mise à jour des informations.'
                    ];
                }
            } catch (PDOException $e) {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Erreur : Un autre employé possède déjà cette CNI ou cet e-mail.'
                ];
            }
        }

        require_once __DIR__ . '/../views/employees/edit.php';
    }

    // Affichage des détails d'un employé
    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("Location: index.php");
            exit;
        }

        $employee = $this->employee->getById($id);

        if (!$employee) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Employé introuvable.'
            ];
            header("Location: index.php");
            exit;
        }

        require_once __DIR__ . '/../views/employees/show.php';
    }

    // Suppression d'un employé
    public function delete() {
        $id = $_GET['id'] ?? null;

        if ($id) {
            try {
                if ($this->employee->delete($id)) {
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => 'L\'employé et son contrat ont été supprimés avec succès !'
                    ];
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Erreur lors de la suppression de l\'employé.'
                    ];
                }
            } catch (PDOException $e) {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Impossible de supprimer cet employé car il est lié à d\'autres enregistrements.'
                ];
            }
        }

        header("Location: index.php");
        exit;
    }

    // Affichage de la page d'importation CSV
    public function import() {
        require_once __DIR__ . '/../views/employees/import.php';
    }

    // Traitement de l'importation du fichier CSV
    public function processImport() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Erreur lors du téléversement du fichier.'
                ];
                header("Location: index.php?action=import");
                exit;
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($extension !== 'csv') {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Veuillez sélectionner un fichier au format .csv.'
                ];
                header("Location: index.php?action=import");
                exit;
            }

            $filePath = $file['tmp_name'];
            $handle = fopen($filePath, 'r');

            if ($handle !== false) {
                // Détection du séparateur (; ou ,)
                $firstLine = fgets($handle);
                $delimiter = (substr_count($firstLine, ';') >= substr_count($firstLine, ',')) ? ';' : ',';
                rewind($handle);

                // Ignorer la ligne d'en-tête (titres des colonnes)
                fgetcsv($handle, 1000, $delimiter);

                $successCount = 0;
                $errorCount = 0;

                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Nettoyage et vérification de l'encodage
                    $cleanRow = array_map(function($value) {
                        $value = trim($value);
                        if (!mb_check_encoding($value, 'UTF-8')) {
                            $value = utf8_encode($value);
                        }
                        return $value;
                    }, $row);

                    $entreprise = $cleanRow[0] ?? 'STC';
                    $nom        = $cleanRow[1] ?? '';
                    $prenom     = $cleanRow[2] ?? '';

                    if (empty($nom) || empty($prenom)) {
                        $errorCount++;
                        continue;
                    }

                    // Gestion du matricule dans l'import : si vide, on génère un matricule auto 6 chiffres
                    $matricule = $cleanRow[14] ?? ''; // ou ajustez l'index selon votre CSV
                    if (empty($matricule)) {
                        $nextId = $this->employee->getNextAutoIncrementId();
                        $matricule = str_pad($nextId, 6, '0', STR_PAD_LEFT);
                    }

                    // Helper pour formater les dates JJ/MM/AAAA vers YYYY-MM-DD
                    $formatDate = function($dateStr) {
                        if (empty($dateStr)) return null;
                        $dateStr = trim($dateStr);
                        if (preg_match('/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/', $dateStr, $matches)) {
                            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                        }
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                            return $dateStr;
                        }
                        return null;
                    };

                    $data = [
                        'matricule'              => $matricule,
                        'entreprise'             => $entreprise,
                        'nom'                    => $nom,
                        'prenom'                 => $prenom,
                        'etat_civil'             => $cleanRow[3] ?: null,
                        'adresse'                => $cleanRow[4] ?: null,
                        'telephone'              => $cleanRow[5] ?: null,
                        'cni'                    => $cleanRow[6] ?: null,
                        'email'                  => $cleanRow[7] ?: null,
                        'intitule_poste'         => $cleanRow[8] ?: null,
                        'type_contrat'           => $cleanRow[9] ?: null,
                        'date_embauche'          => $formatDate($cleanRow[10] ?? ''),
                        'numero_permis'          => $cleanRow[11] ?: null,
                        'categories_permis'      => $cleanRow[12] ?: null,
                        'date_expiration_permis' => $formatDate($cleanRow[13] ?? ''),
                        'contrat_pdf'            => null
                    ];

                    try {
                        if ($this->employee->importOrUpdate($data)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } catch (PDOException $e) {
                        $errorCount++;
                    }
                }

                fclose($handle);

                if ($successCount > 0) {
                    $msg = "Importation réussie : <strong>{$successCount}</strong> employé(s) traité(s).";
                    if ($errorCount > 0) {
                        $msg .= " (<strong>{$errorCount}</strong> ligne(s) ignorée(s) ou en erreur).";
                    }
                    $_SESSION['flash'] = [
                        'type' => 'success',
                        'message' => $msg
                    ];
                } else {
                    $_SESSION['flash'] = [
                        'type' => 'danger',
                        'message' => 'Aucune donnée n\'a été importée. Vérifiez le format de votre fichier.'
                    ];
                }

                header("Location: index.php");
                exit;
            } else {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Impossible d\'ouvrir le fichier téléversé.'
                ];
                header("Location: index.php?action=import");
                exit;
            }
        }
    }

    // Télécharger un modèle d'exemple au format CSV
    public function downloadTemplate() {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=modele_importation_employes.csv');

        $output = fopen('php://output', 'w');
        
        // BOM UTF-8 pour un affichage correct des accents dans Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // En-têtes incluant l'entreprise
        fputcsv($output, [
            'Entreprise (SITCOM/STC/TNDF)', 
            'Nom', 
            'Prenom', 
            'Etat Civil', 
            'Adresse', 
            'Telephone', 
            'CNI', 
            'Email', 
            'Intitule Poste', 
            'Type Contrat', 
            'Date Embauche (JJ/MM/AAAA)', 
            'Numero Permis', 
            'Categories Permis', 
            'Date Expiration Permis (JJ/MM/AAAA)'
        ], ';');

        // Ligne d'exemple 1
        fputcsv($output, [
            'STC', 
            'Diop', 
            'Mamadou', 
            'Marié(e)', 
            'Dakar, Plateau', 
            '771234567', 
            '1759199000123', 
            'm.diop@example.com', 
            'Comptable', 
            'CDI', 
            '15/01/2022', 
            'PERM-9876', 
            'B', 
            '20/12/2028'
        ], ';');

        // Ligne d'exemple 2
        fputcsv($output, [
            'SITCOM', 
            'Ndiaye', 
            'Amina', 
            'Célibataire', 
            'Gédiawaye', 
            '789876543', 
            '2759199500456', 
            'a.ndiaye@example.com', 
            'Développeur PHP', 
            'CDD', 
            '01/06/2023', 
            '', 
            '', 
            ''
        ], ';');

        fclose($output);
        exit;
    }
}