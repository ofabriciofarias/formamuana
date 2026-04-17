<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {

    // -------------------------------------------------------------------------
    // Público
    // -------------------------------------------------------------------------
    $r->addRoute('GET',  '/',                       ['HomeController', 'index']);
    $r->addRoute('GET',  '/login',                  ['UserController', 'showLogin']);
    $r->addRoute('POST', '/login',                  ['UserController', 'do_login']);
    $r->addRoute('GET',  '/logout',                 ['UserController', 'logout']);
    $r->addRoute('GET',  '/register',               ['UserController', 'showRegister']);
    $r->addRoute('POST', '/register',               ['UserController', 'register']);
    $r->addRoute('POST', '/recuperar-senha',        ['UserController', 'recoverPassword']);

    // -------------------------------------------------------------------------
    // Aluno — cadastro e inscrição
    // -------------------------------------------------------------------------
    $r->addRoute('GET',  '/aluno/cadastro',         ['AlunoController', 'showAlunoRegistrationForm']);
    $r->addRoute('POST', '/aluno/cadastro',         ['AlunoController', 'registerAluno']);
    $r->addRoute('GET',  '/aluno/inscricao',        ['AlunoController', 'showInscricaoForm']);
    $r->addRoute('POST', '/aluno/inscricao',        ['AlunoController', 'doInscricaoOferta']);
    $r->addRoute('GET',  '/aluno/dashboard',        ['AlunoController', 'dashboard']);
    $r->addRoute('POST', '/aluno/atualizar',        ['AlunoController', 'updateAluno']);

    // -------------------------------------------------------------------------
    // Perfil (qualquer usuário autenticado)
    // -------------------------------------------------------------------------
    $r->addRoute('GET',  '/perfil',                 ['UserController', 'get_profile']);
    $r->addRoute('POST', '/perfil',                 ['UserController', 'update_profile']);

    // -------------------------------------------------------------------------
    // Admin — dashboard e relatórios
    // -------------------------------------------------------------------------
    $r->addRoute('GET',  '/admin',                  ['DashboardController', 'index']);
    $r->addRoute('GET',  '/admin/relatorio',        ['DashboardController', 'relatorioGeral']);
    $r->addRoute('GET',  '/admin/relatorio/detalhado', ['DashboardController', 'relatorioDetalhado']);
    $r->addRoute('GET',  '/admin/comunicacao',      ['DashboardController', 'comunicacao']);
    $r->addRoute('POST', '/admin/email',            ['DashboardController', 'sendEmailNotification']);

    // Admin — seleção de alunos
    $r->addRoute('GET',  '/admin/selecao',          ['DashboardController', 'selecaoAlunos']);
    $r->addRoute('POST', '/admin/selecao/status',   ['DashboardController', 'updateStatus']);
    $r->addRoute('POST', '/admin/selecao/status/ajax', ['DashboardController', 'updateStatusAjax']);
    $r->addRoute('POST', '/admin/selecao/ga',       ['DashboardController', 'runGASelection']);

    // Admin — inscritos
    $r->addRoute('GET',  '/admin/inscritos',        ['DashboardController', 'visualizarInscritos']);
    $r->addRoute('GET',  '/admin/inscrito',         ['DashboardController', 'getInscrito']);
    $r->addRoute('POST', '/admin/inscrito/atualizar', ['DashboardController', 'updateInscrito']);

    // Admin — vínculo de professor
    $r->addRoute('GET',  '/admin/vincular-professor',       ['DashboardController', 'vincularProfessor']);
    $r->addRoute('POST', '/admin/professor/vincular',       ['DashboardController', 'ajaxLinkProfessor']);
    $r->addRoute('POST', '/admin/professor/desvincular',    ['DashboardController', 'ajaxUnlinkProfessor']);

    // Admin — vínculo de aluno
    $r->addRoute('GET',  '/admin/vincular-aluno',           ['DashboardController', 'vincularAluno']);
    $r->addRoute('GET',  '/admin/alunos/buscar',            ['DashboardController', 'ajaxBuscarAlunosParaVinculo']);
    $r->addRoute('POST', '/admin/aluno/vincular',           ['DashboardController', 'ajaxVincularAluno']);

    // Admin — ofertas
    $r->addRoute('GET',  '/admin/oferta/nova',      ['OfertaController', 'create']);
    $r->addRoute('POST', '/admin/oferta',           ['OfertaController', 'store']);

    // -------------------------------------------------------------------------
    // Professor
    // -------------------------------------------------------------------------
    $r->addRoute('GET',  '/professor/dashboard',            ['ProfessorController', 'dashboard']);
    $r->addRoute('GET',  '/professor/frequencia',           ['ProfessorController', 'lancarFrequencia']);
    $r->addRoute('POST', '/professor/frequencia',           ['ProfessorController', 'salvarFrequencia']);
    $r->addRoute('GET',  '/professor/frequencia/consultar', ['ProfessorController', 'consultarFrequencia']);
    $r->addRoute('GET',  '/professor/notas',                ['ProfessorController', 'notas']);
    $r->addRoute('POST', '/professor/notas',                ['ProfessorController', 'salvarNotas']);
    $r->addRoute('GET',  '/professor/inscritos',            ['DashboardController', 'visualizarInscritos']);
    $r->addRoute('GET',  '/professor/vincular-aluno',       ['DashboardController', 'vincularAluno']);
};
