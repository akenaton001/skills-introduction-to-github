<?php
require_once 'models/Transaction.php';

class TransactionController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function create() {
        $view = 'views/transactions/create.php';
        include 'views/layout.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transaction = new Transaction($this->db);
            
            // Vérification si la rubrique est "Vente de terrain" pour récupérer les infos spécifiques
            $rubrique = $_POST['rubrique'] ?? '';
            $isTerrain = ($rubrique === 'Vente de terrain');

            $data = [
                'date_transaction' => $_POST['date_transaction'],
                'type' => $_POST['type'],
                'libelle' => $_POST['libelle'],
                'montant' => $_POST['montant'],
                'rubrique' => $rubrique,
                'beneficiaire' => !empty($_POST['beneficiaire']) ? $_POST['beneficiaire'] : null,
                'banque' => $_POST['banque'],
                // Champs spécifiques terrain (null si ce n'est pas une vente de terrain)
                'cessionnaire' => $isTerrain ? (!empty($_POST['cessionnaire']) ? $_POST['cessionnaire'] : null) : null,
                'nombre_terrain' => $isTerrain ? (!empty($_POST['nombre_terrain']) ? $_POST['nombre_terrain'] : null) : null,
                'prix_unitaire' => $isTerrain ? (!empty($_POST['prix_unitaire']) ? $_POST['prix_unitaire'] : null) : null,
                'commission' => $isTerrain ? (!empty($_POST['commission']) ? $_POST['commission'] : null) : null,
                'type_paiement' => $isTerrain ? (!empty($_POST['type_paiement']) ? $_POST['type_paiement'] : null) : null
            ];

            $transaction->create($data);
            header('Location: index.php?action=list');
            exit();
        }
    }

    public function index() {
        $transaction = new Transaction($this->db);
        $filters = [
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? '',
            'type' => $_GET['type'] ?? '',
            'rubrique' => $_GET['rubrique'] ?? '',
            'beneficiaire' => $_GET['beneficiaire'] ?? '',
            'banque' => $_GET['banque'] ?? ''
        ];
        $transactions = $transaction->getFiltered($filters);
        $view = 'views/transactions/list.php';
        include 'views/layout.php';
    }

    // Afficher le formulaire de modification
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?action=list');
            exit();
        }

        $transactionModel = new Transaction($this->db);
        $transaction = $transactionModel->getById($id);
        
        $view = 'views/transactions/edit.php';
        include 'views/layout.php';
    }

    // Traiter la mise à jour
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $transactionModel = new Transaction($this->db);
            
            $rubrique = $_POST['rubrique'] ?? '';
            $isTerrain = ($rubrique === 'Vente de terrain');

            $data = [
                'date_transaction' => $_POST['date_transaction'],
                'type' => $_POST['type'],
                'libelle' => $_POST['libelle'],
                'montant' => $_POST['montant'],
                'rubrique' => $rubrique,
                'beneficiaire' => !empty($_POST['beneficiaire']) ? $_POST['beneficiaire'] : null,
                'banque' => $_POST['banque'],
                'cessionnaire' => $isTerrain ? (!empty($_POST['cessionnaire']) ? $_POST['cessionnaire'] : null) : null,
                'nombre_terrain' => $isTerrain ? (!empty($_POST['nombre_terrain']) ? $_POST['nombre_terrain'] : null) : null,
                'prix_unitaire' => $isTerrain ? (!empty($_POST['prix_unitaire']) ? $_POST['prix_unitaire'] : null) : null,
                'commission' => $isTerrain ? (!empty($_POST['commission']) ? $_POST['commission'] : null) : null,
                'type_paiement' => $isTerrain ? (!empty($_POST['type_paiement']) ? $_POST['type_paiement'] : null) : null
            ];

            $transactionModel->update($id, $data);
            header('Location: index.php?action=list');
            exit();
        }
    }

    // Supprimer une transaction
    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $transactionModel = new Transaction($this->db);
            $transactionModel->delete($id);
        }
        header('Location: index.php?action=list');
        exit();
    }
}
?>