<?php
require_once 'models/Transaction.php';

class ReportController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    // Page 1 : Rapport par rubrique (Synthèse globale cumulée par rubrique)
    public function rubrique() {
        $transaction = new Transaction($this->db);
        $years = $transaction->getAvailableYears();
        
        // Récupération des filtres sélectionnés via le formulaire GET
        $selectedYear = $_GET['annee'] ?? '';
        $selectedMonth = $_GET['mois'] ?? '';

        // Récupération de toutes les rubriques et de la synthèse globale filtrée
        $allRubriques = $transaction->getAllRubriques();
        $reports = $transaction->getRubriqueSummary($selectedYear, $selectedMonth);
        
        // Inclusion du layout global de l'application
        $view = 'views/reports/rubrique.php';
        include 'views/layout.php';
    }

    // Page 2 : Affichage journalier par rubrique filtrable par mois et année
    public function journalier() {
        $transaction = new Transaction($this->db);
        $years = $transaction->getAvailableYears();
        
        // Laissés vides par défaut pour tout afficher à l'ouverture
        $selectedYear = $_GET['annee'] ?? '';
        $selectedMonth = $_GET['mois'] ?? '';

        $dailyReports = $transaction->getDailyReportByRubrique($selectedYear, $selectedMonth);
        
        $view = 'views/reports/journalier.php';
        include 'views/layout.php';
    }

    // Page 3 : Rapport et suivi de tous les bénéficiaires
    public function beneficiaire() {
        $transaction = new Transaction($this->db);
        $years = $transaction->getAvailableYears();
        
        // Les filtres d'année et de mois sont gérés directement dans la vue pour chaque bénéficiaire
        $view = 'views/reports/beneficiaire.php';
        include 'views/layout.php';
    }
}
?>