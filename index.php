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
require_once __DIR__ . '/controllers/ProfessorController.php';

$userController = new UserController();
$dashboardController = new DashboardController();
$homeController = new HomeController();
$alunoController = new AlunoController();
$ofertaController = new OfertaController();
$professorController = new ProfessorController();

// 4. Executar a ação correspondente
switch ($action) {
    case 'do_login':
        $userController->do_login();
        break;
        
    case 'recover_password':
        $userController->recoverPassword();
        break;

    case 'logout':
        $userController->logout();
        break;

    case 'register':
        $userController->register();
        break;

    case 'validar_certificado':
        $homeController->validarCertificado();
        break;

    case 'show_register_aluno_form':        $alunoController->showAlunoRegistrationForm();
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

    case 'relatorio_detalhado':
        $dashboardController->relatorioDetalhado();
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

    case 'vincular_professor':
        $dashboardController->vincularProfessor();
        break;

    case 'ajaxLinkProfessor':
        $dashboardController->ajaxLinkProfessor();
        break;

    case 'ajaxUnlinkProfessor':
        $dashboardController->ajaxUnlinkProfessor();
        break;

    case 'vincular_aluno':
    case 'professor_vincular_aluno':
        $dashboardController->vincularAluno();
        break;

    case 'ajaxBuscarAlunosParaVinculo':
        $dashboardController->ajaxBuscarAlunosParaVinculo();
        break;

    case 'ajaxVincularAluno':
        $dashboardController->ajaxVincularAluno();
        break;

    case 'professor_dashboard':
        $professorController->dashboard();
        break;

    case 'lancar_frequencia':
        $professorController->lancarFrequencia();
        break;

    case 'salvar_frequencia': // AJAX
        $professorController->salvarFrequencia();
        break;

    case 'consultar_frequencia':
        $professorController->consultarFrequencia();
        break;

    case 'notas':
        $professorController->notas();
        break;

    case 'salvarNotas':
        $professorController->salvarNotas();
        break;

    case 'comunicacao':
        $dashboardController->comunicacao();
        break;

    case 'vincular_certificados':
        $dashboardController->vincularCertificados();
        break;

    case 'upload_certificado':
        $dashboardController->uploadCertificado();
        break;

    case 'visualizar_inscritos':
    case 'professor_visualizar_inscritos':
        $dashboardController->visualizarInscritos();
        break;

    case 'get_inscrito':
        $dashboardController->getInscrito();
        break;

    case 'update_inscrito':
        $dashboardController->updateInscrito();
        break;

    case 'get_profile':
        $userController->get_profile();
        break;

    case 'update_profile':
        $userController->update_profile();
        break;

    case 'registro-sucesso':
        echo "Registro efetuado com sucesso!";
        echo '<a href="index.php?action=retistro.php">Voltar</a>';
        break;

    default:
        // Ação padrão: Se for admin, vai para a página de registro como dashboard principal.
        if (is_admin()) {
            header('Location: index.php?action=dashboard');
            exit;
        } elseif (is_professor()) {
            header('Location: index.php?action=professor_dashboard');
            exit;
        } else {
            $homeController->index();
        }
        break;
}
