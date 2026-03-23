<?php
// Main Front Controller

// 0. Carregar o autoloader do Composer
require_once __DIR__ . '/vendor/autoload.php';

// 1. Carregar arquivos de configuração e núcleo
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mail.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/helpers.php';

// 2. Roteamento
$action = $_GET['action'] ?? 'default';

// 3. Incluir controladores
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/DashboardController.php';
require_once __DIR__ . '/controllers/HomeController.php';
require_once __DIR__ . '/controllers/AlunoController.php';
require_once __DIR__ . '/controllers/OfertaController.php';

$userController = new UserController();
$dashboardController = new DashboardController();
$homeController = new HomeController();
$alunoController = new AlunoController();
$ofertaController = new OfertaController();

// 4. Executar a ação correspondente
switch ($action) {
    case 'do_login':
        $userController->do_login();
        break;

    case 'logout':
        $userController->logout();
        break;

    case 'register':
        $userController->register();
        break;
    
    case 'show_register_aluno_form':
        $alunoController->showAlunoRegistrationForm();
        break;

    case 'register_aluno':
        $alunoController->registerAluno();
        break;

    case 'show_inscricao_oferta_form':
        $alunoController->showInscricaoForm();
        break;

    case 'do_inscricao_oferta':
        $alunoController->doInscricaoOferta();
        break;

    case 'dashboard':
        $dashboardController->index();
        break;

    case 'aluno_dashboard':
        $alunoController->dashboard();
        break;

    case 'update_aluno':
        $alunoController->updateAluno();
        break;

    case 'register-oferta':
        $ofertaController->create();
        break;

    case 'store-oferta':
        $ofertaController->store();
        break;

    case 'relatorio_geral':
        $dashboardController->relatorioGeral();
        break;

    case 'updateStatus':
        $dashboardController->updateStatus();
        break;

    case 'selecao_alunos':
        $dashboardController->selecaoAlunos();
        break;

    case 'updateStatusAjax':
        $dashboardController->updateStatusAjax();
        break;

    case 'run_ga_selection':
        $dashboardController->runGASelection();
        break;

    case 'send_email_notification':
        $dashboardController->sendEmailNotification();
        break;

    case 'comunicacao':
        $dashboardController->comunicacao();
        break;

    case 'registro-sucesso':
        echo "Registro efetuado com sucesso!";
        echo '<a href="index.php?action=retistro.php">Voltar</a>';
        break;

    default:
        // Ação padrão: Se for admin, vai para a página de registro como dashboard principal.
        // Senão, mostra a landing page pública.
        if (is_admin()) {
            // A página principal para um admin logado é a de registro de usuários.
            header('Location: index.php?action=register');
            exit;
        } else {
            $homeController->index();
        }
        break;
}
