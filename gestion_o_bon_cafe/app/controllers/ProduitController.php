<?php
require_once '../app/models/Produit.php';
require_once '../app/models/Categorie.php';
require_once '../core/Database.php';

class ProduitController {
    private $db;
    private $produitModel;
    private $categorieModel;

    public function __construct() {
        // Sécurité : Seul l'admin gère les produits
        if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=rapport&action=index");
            exit();
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->produitModel = new Produit($this->db);
        $this->categorieModel = new Categorie($this->db);
    }

    public function index() {
        $produits = $this->produitModel->listerTous();
        require_once '../app/views/produits/index.php';
    }

    public function creer() {
        $categories = $this->categorieModel->listerTous();

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = trim($_POST['nom']);
            $categorie_id = $_POST['categorie_id'];
            $prix_vente = $_POST['prix_vente'];
            $type_produit = $_POST['type_produit'];
            $stock = isset($_POST['stock']) ? $_POST['stock'] : 0;

            if(!empty($nom) && !empty($prix_vente)) {
                $this->produitModel->ajouter($nom, $categorie_id, $prix_vente, $stock, $type_produit);
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index");
                exit();
            }
        }
        require_once '../app/views/produits/creer.php';
    }

    // ACTION : Éditer un produit existant
    public function editer() {
        if(!isset($_GET['id'])) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index");
            exit();
        }

        $id = $_GET['id'];
        $produit = $this->produitModel->trouverParId($id);
        $categories = $this->categorieModel->listerTous();

        if(!$produit) {
            header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index");
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nom = trim($_POST['nom']);
            $categorie_id = $_POST['categorie_id'];
            $prix_vente = $_POST['prix_vente'];
            $type_produit = $_POST['type_produit'];
            $stock = isset($_POST['stock']) ? $_POST['stock'] : 0;

            if(!empty($nom) && !empty($prix_vente)) {
                $this->produitModel->mettreAJour($id, $nom, $categorie_id, $prix_vente, $stock, $type_produit);
                header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index&succes=1");
                exit();
            }
        }

        require_once '../app/views/produits/editer.php';
    }

    // ACTION : Réapprovisionner rapidement le stock
    public function reapprovisionner() {
        if(isset($_GET['id']) && isset($_POST['quantite_ajouter'])) {
            $id = $_GET['id'];
            $quantite = intval($_POST['quantite_ajouter']);
            if($quantite > 0) {
                $this->produitModel->ajouterStock($id, $quantite);
            }
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index");
        exit();
    }

    public function supprimer() {
        if(isset($_GET['id'])) {
            $this->produitModel->supprimer($_GET['id']);
        }
        header("Location: /gestion_o_bon_cafe/public/index.php?controller=produit&action=index");
        exit();
    }
}