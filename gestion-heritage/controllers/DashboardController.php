<?php
require_once 'models/Transaction.php';

class DashboardController {
    private $db;
    public function __construct($db) { $this->db = $db; }

    public function index() {
        $transaction = new Transaction($this->db);
        $totals = $transaction->getTotals();
        
        // Totaux par banque (prenant en compte entrées / sorties / versements / retraits)
        $bis = $transaction->getTotalsByBank('BIS');
        $bhs = $transaction->getTotalsByBank('BHS');
        $cbao = $transaction->getTotalsByBank('CBAO');

        $view = 'views/dashboard.php';
        include 'views/layout.php';
    }
}
?>