<?php

require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';

class DashboardController {

    public function index() {
        // As funções de segurança e controle de acesso do exemplo do usuário
        // foram adaptadas no `helpers.php`
        if (!is_admin() && !is_dentista() && !is_recepcionista()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
            exit;
        }

        // --- AVISO ---
        // A lógica de negócio e as consultas SQL fornecidas no exemplo
        // (faturamento, lucro, despesas, atendimentos) não correspondem
        // ao esquema do banco de dados atual ('forma_muana' com alunos, ofertas, etc.).
        // Os valores a seguir são apenas placeholders.
        // Será necessário reescrever as consultas para refletir a estrutura de dados do projeto.

        $mes_selecionado = date('Y-m');
        $faturamentoBruto = 0;
        $lucroLiquido = 0;
        $totalDespesas = 0;
        $ultimosAtendimentos = [];
        $totalPaginas = 0;
        $busca = '';
        $mes_anterior = date('Y-m', strtotime(date('Y-m-01') . ' -1 month'));
        $mes_proximo = date('Y-m', strtotime(date('Y-m-01') . ' +1 month'));
        $mesAtual = 'Mês Atual';

        // Carrega a view do dashboard
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
