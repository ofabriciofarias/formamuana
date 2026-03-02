<?php

require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/AlunoOferta.php';

class DashboardController {

    public function index() {
        if (!is_admin() && !is_professor()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit;
        }

        // Carrega a view do dashboard
        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    public function relatorioGeral() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $ofertaId = $_GET['oferta_id'] ?? null;
        $buscaNome = $_GET['busca'] ?? null;

        // Busca todas as ofertas para o filtro
        $ofertas = Oferta::findAll();

        // Busca os inscritos se uma oferta for selecionada ou se houver busca
        $inscritos = [];
        if ($ofertaId || $buscaNome) {
            $inscritos = AlunoOferta::getRelatorio($ofertaId, $buscaNome);
        }

        require_once __DIR__ . '/../views/dashboard/relatorio.php';
    }

    public function updateStatus() {
        if (!is_admin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        $alunoId = $_POST['aluno_id'];
        $ofertaId = $_POST['oferta_id'];
        $status = $_POST['status_selecao'];
        
        // Redirecionamento mantendo os filtros
        $redirectUrl = "index.php?action=relatorio_geral&oferta_id=" . $_POST['current_oferta_id'] . "&busca=" . $_POST['current_busca'];

        AlunoOferta::updateStatus($alunoId, $ofertaId, $status);
        
        header("Location: $redirectUrl");
        exit;
    }
}
