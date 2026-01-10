<?php
require_once 'controller/auth_controller.php';
require_once 'model/AnalyticsModel.php';

class AdminAnalyticsController
{
    public function index()
    {
        AuthController::checkAdmin();

        // Rango por defecto: últimos 14 días
        $days = (int)($_GET['days'] ?? 14);
        if ($days <= 0 || $days > 365) $days = 14;

        $to = date('Y-m-d 23:59:59');
        $from = date('Y-m-d 00:00:00', strtotime("-$days days"));

        $m = new AnalyticsModel();

        $kpis = $m->kpis($from, $to);
        $topProducts = $m->topProducts($from, $to, 6);
        $byDay = $m->salesByDay($from, $to);
        $byCategory = $m->salesByCategory($from, $to);

        ob_start();
        require 'views/admin/analytics/index.php';
        $viewContent = ob_get_clean();
        require 'views/layouts/main.php';
    }
}
