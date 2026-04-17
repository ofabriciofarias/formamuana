<?php

require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/GeneticAlgorithm.php';
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/AlunoOferta.php';

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class DashboardController {

    public function index() {
        if (!is_admin() && !is_professor()) {
            header('Location: /login');
            exit;
        }

        // Carrega a view do dashboard
        require_once __DIR__ . '/../views/dashboard/index.php';
    }

    //Relatório detalhado
    public function relatorioDetalhado() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Total de alunos cadastrados
        $stmtTotal = $db->query("SELECT COUNT(*) FROM alunos");
        $totalAlunos = (int)$stmtTotal->fetchColumn();

        // Total de alunos inscritos em algum curso (únicos)
        $stmtInscritos = $db->query("SELECT COUNT(DISTINCT alunos_id) FROM alunos_has_oferta");
        $totalInscritosUnicos = (int)$stmtInscritos->fetchColumn();

        // Tabela contendo os inscritos por curso e estatísticas
        $stmtCursos = $db->query("
            SELECT 
                o.id,
                o.nome as curso, 
                o.turno,
                o.numero_vagas,
                COUNT(aho.alunos_id) as total_inscritos,
                SUM(CASE WHEN aho.status_selecao = 'Aprovado' THEN 1 ELSE 0 END) as total_aprovados
            FROM oferta o
            LEFT JOIN alunos_has_oferta aho ON o.id = aho.oferta_id
            GROUP BY o.id, o.nome, o.turno, o.numero_vagas
            ORDER BY o.nome ASC
        ");
        $estatisticasCursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

        // Tabela agrupando as turmas pelo nome do curso
        $stmtAgrupados = $db->query("
            SELECT 
                sub.curso,
                SUM(sub.numero_vagas) as total_vagas,
                SUM(sub.total_inscritos) as total_inscritos,
                SUM(sub.total_aprovados) as total_aprovados
            FROM (
                SELECT 
                    o.id,
                    o.nome as curso, 
                    o.numero_vagas,
                    COUNT(aho.alunos_id) as total_inscritos,
                    SUM(CASE WHEN aho.status_selecao = 'Aprovado' THEN 1 ELSE 0 END) as total_aprovados
                FROM oferta o
                LEFT JOIN alunos_has_oferta aho ON o.id = aho.oferta_id
                GROUP BY o.id, o.nome, o.numero_vagas
            ) sub
            GROUP BY sub.curso
            ORDER BY sub.curso ASC
        ");
        $estatisticasAgrupadas = $stmtAgrupados->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/dashboard/estatisticas.php';
    }
    //Fim do relatório detalhado.

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

        $alunoId = $_POST['aluno_id'] ?? null;
        $ofertaId = $_POST['oferta_id'] ?? null;
        $status = $_POST['status_selecao'] ?? null;
        $redirectTo = $_POST['redirect_to'] ?? 'relatorio'; // Default para o comportamento antigo

        // Redirecionamento mantendo os filtros
        $redirectUrl = "/admin/" . $redirectTo . "?oferta_id=" . ($_POST['current_oferta_id'] ?? '') . "&busca=" . ($_POST['current_busca'] ?? '');

        if ($alunoId && $ofertaId && $status) {
            AlunoOferta::updateStatus($alunoId, $ofertaId, $status);
        }
        
        header("Location: $redirectUrl");
        exit;
    }

    public function selecaoAlunos() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $ofertaId = $_GET['oferta_id'] ?? null;
        
        // Busca todas as ofertas para o dropdown
        $ofertas = Oferta::findAll();

        // Busca os inscritos apenas se uma oferta for selecionada
        $inscritos = [];
        $inscricoesEncerradas = false; // Variável para controlar o estado das inscrições

        if ($ofertaId) {
            // Reutiliza o método do model que já faz o JOIN necessário
            $inscritos = AlunoOferta::getRelatorio($ofertaId, null);

            if (!empty($inscritos) && isset($inscritos[0]['data_fim_inscricao'])) {
                try {
                    $dataFimInscricao = new DateTime($inscritos[0]['data_fim_inscricao']);
                    $hoje = new DateTime();

                    // As inscrições são consideradas encerradas se a data atual for POSTERIOR à data de fim.
                    // setTime(0,0,0) para comparar apenas as datas.
                    if ($hoje->setTime(0, 0, 0) > $dataFimInscricao->setTime(0, 0, 0)) {
                        $inscricoesEncerradas = true;
                    }
                } catch (Exception $e) {
                    // Lidar com data inválida, se necessário
                    $inscricoesEncerradas = false;
                }
            }
        }

        require_once __DIR__ . '/../views/dashboard/selecao.php';
    }

    public function comunicacao() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $ofertaId = $_GET['oferta_id'] ?? null;
        
        // Busca todas as ofertas para o dropdown
        $ofertas = Oferta::findAll();

        // Busca os inscritos apenas se uma oferta for selecionada
        $inscritos = [];
        if ($ofertaId) {
            $inscritos = AlunoOferta::getRelatorio($ofertaId, null);
        }

        require_once __DIR__ . '/../views/dashboard/comunicacao.php';
    }


    public function updateStatusAjax() {
        header('Content-Type: application/json');
        
        if (!is_admin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $result = AlunoOferta::updateStatus($data['aluno_id'], $data['oferta_id'], $data['status']);
        
        echo json_encode(['success' => $result]);
        exit;
    }

    public function runGASelection() {
        // Aumenta o tempo limite de execução para processos longos e impede que o script pare se o usuário fechar a conexão.
        set_time_limit(0);
        ignore_user_abort(true);

        header('Content-Type: application/json');
        
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $ofertaId = $data['oferta_id'] ?? null;
        $populationSize = (int)($data['population_size'] ?? 50);
        $generations = (int)($data['generations'] ?? 100);
        $mutationRate = (float)($data['mutation_rate'] ?? 0.1);
        // Parâmetros para o modo híbrido
        $mode = $data['mode'] ?? 'ga'; // 'ga' ou 'hybrid'
        $nlpWeight = (float)($data['nlp_weight'] ?? 0.3);

        if (!$ofertaId) {
            echo json_encode(['success' => false, 'message' => 'ID da oferta é obrigatório.']);
            exit;
        }

        // 1. Obter todos os inscritos e os dados da oferta (incluindo numero_vagas)
        $inscritos = AlunoOferta::getRelatorio($ofertaId, null);
        if (empty($inscritos)) {
            echo json_encode(['success' => false, 'message' => 'Nenhum aluno inscrito para esta oferta.']);
            exit;
        }

        $numeroVagas = (int)($inscritos[0]['numero_vagas'] ?? 0);
        if ($numeroVagas <= 0) {
            echo json_encode(['success' => false, 'message' => 'O número de vagas para esta oferta não está definido ou é zero.']);
            exit;
        }
        
        if (count($inscritos) <= $numeroVagas) {
            // Se o número de inscritos for menor ou igual ao de vagas, aprova todos.
            foreach ($inscritos as $inscrito) {
                AlunoOferta::updateStatus($inscrito['alunos_id'], $ofertaId, 'Aprovado');
            }
            echo json_encode(['success' => true, 'message' => 'Todos os inscritos foram aprovados pois o número de candidatos é menor ou igual ao número de vagas.']);
            exit;
        }

        // 2. Definir a função de Fitness (Cálculo de Vulnerabilidade)
        // A função de fitness agora pode operar em modo 'ga' (padrão) ou 'hybrid'
        $fitnessFunction = function($aluno) use ($mode, $nlpWeight) {
            // --- Parte 1: Pontuação Socioeconômica (Sempre calculada) ---
            $socioEconomicScore = 0;
            // Critérios de Renda e Social
            if ($aluno['renda_familiar'] === 'Até 1 salário mínimo') $socioEconomicScore += 50;
            elseif ($aluno['renda_familiar'] === 'De 1 a 2 salários mínimos') $socioEconomicScore += 30;
            elseif ($aluno['renda_familiar'] === 'De 2 a 3 salários mínimos') $socioEconomicScore += 10;

            if (isset($aluno['beneficiario_programa_social']) && $aluno['beneficiario_programa_social'] !== 'Nenhum' && !empty($aluno['beneficiario_programa_social'])) $socioEconomicScore += 40;
            
            // Critérios de Moradia e Origem
            if (isset($aluno['zona_residencia']) && $aluno['zona_residencia'] === 'Rural') $socioEconomicScore += 20;
            if (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] !== 'Nenhuma' && !empty($aluno['comunidade_tradicional'])) $socioEconomicScore += 25;

            // Critérios Pessoais
            if (isset($aluno['cor_raca']) && in_array($aluno['cor_raca'], ['Preta', 'Parda', 'Indígena'])) $socioEconomicScore += 15;
            if (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] === 'Sim') $socioEconomicScore += 20;
            if (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] === 'Sim') $socioEconomicScore += 10 + ((int)$aluno['quantidade_filhos'] * 2);

            // Critérios de Educação e Trabalho
            if (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] === 'Pública') $socioEconomicScore += 15;
            if (isset($aluno['exerce_atividade_remunerada']) && $aluno['exerce_atividade_remunerada'] === 'Nao') $socioEconomicScore += 10;

            // Se o modo for 'ga', retorna apenas a pontuação socioeconômica
            if ($mode !== 'hybrid') {
                return $socioEconomicScore;
            }

            // --- Parte 2: Pontuação de Análise de Texto (Apenas no modo híbrido) ---
            $nlpScore = $this->_calculateNlpScore($aluno);

            // --- Parte 3: Combinação Ponderada das Pontuações ---
            // Normalização das pontuações para uma escala de 0 a 100 para uma ponderação justa.
            // Valores máximos estimados. Podem ser ajustados para maior precisão.
            define('MAX_SOCIO_SCORE', 210);
            define('MAX_NLP_SCORE', 60);

            $normalizedSocioScore = ($socioEconomicScore / MAX_SOCIO_SCORE) * 100;
            $normalizedNlpScore = ($nlpScore / MAX_NLP_SCORE) * 100;

            // A pontuação final é uma média ponderada das duas pontuações normalizadas.
            $finalScore = ((1 - $nlpWeight) * $normalizedSocioScore) + ($nlpWeight * $normalizedNlpScore);

            return $finalScore;
        };

        // 3. Preparar dados para o Algoritmo Genético
        $candidates = array_map(function($inscrito) {
            $inscrito['id'] = $inscrito['alunos_id']; // O GA espera uma chave 'id'
            return $inscrito;
        }, $inscritos);

        // 4. Executar o Algoritmo Genético
        try {
            $ga = new GeneticAlgorithm($candidates, $numeroVagas, $fitnessFunction);
            $result = $ga->run($populationSize, $generations, $mutationRate);
            $bestSelectionIds = $result['chromosome'];

            // 5. Atualizar o status dos alunos no banco de dados
            foreach ($candidates as $candidate) {
                $status = in_array($candidate['id'], $bestSelectionIds) ? 'Aprovado' : 'Lista de Espera';
                AlunoOferta::updateStatus($candidate['id'], $ofertaId, $status);
            }

            echo json_encode(['success' => true, 'message' => 'Seleção automática concluída com sucesso!']);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao executar o algoritmo: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Calcula uma pontuação baseada na análise de palavras-chave das respostas textuais do aluno.
     * Esta é uma abordagem simples de Processamento de Linguagem Natural (PLN).
     * Algoritmos mais avançados como TF-IDF, Word2Vec ou Transformers (BERT) poderiam ser usados
     * para uma análise mais sofisticada, mas requerem bibliotecas e infraestrutura mais complexas.
     *
     * @param array $aluno Dados do aluno, incluindo as respostas textuais.
     * @return int Pontuação calculada.
     */
    private function _calculateNlpScore(array $aluno): int {
        $score = 0;
        $textFields = [
            $aluno['motivo_escolha_curso'] ?? '',
            $aluno['o_que_mais_espera_do_curso'] ?? '',
            $aluno['expectativas_relacao_curso'] ?? '',
            $aluno['expectativas_profissionais_pos_curso'] ?? ''
        ];
        $fullText = mb_strtolower(implode(' ', $textFields), 'UTF-8');

        // Palavras-chave positivas com seus respectivos pesos.
        // Demonstram motivação, alinhamento com o curso e visão de futuro.
        $positiveKeywords = [
            'aprender' => 5, 'oportunidade' => 5, 'crescimento' => 5, 'carreira' => 4,
            'profissional' => 4, 'mercado de trabalho' => 4, 'sonho' => 3, 'evoluir' => 3,
            'conhecimento' => 3, 'desenvolver' => 3, 'ajudar' => 2, 'comunidade' => 2,
            'futuro' => 2, 'objetivo' => 4, 'qualificar' => 4, 'experiência' => 3
        ];

        // Contabiliza cada palavra-chave apenas uma vez para evitar pontuação inflada.
        foreach ($positiveKeywords as $keyword => $value) {
            if (strpos($fullText, $keyword) !== false) {
                $score += $value;
            }
        }

        // Adiciona pontos por respostas mais detalhadas e bem elaboradas.
        $textLength = mb_strlen($fullText, 'UTF-8');
        if ($textLength > 400) $score += 10;
        elseif ($textLength > 200) $score += 5;
        elseif ($textLength > 80) $score += 2;

        return $score;
    }

    public function sendEmailNotification() {
        header('Content-Type: application/json');
        
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $ofertaId = $data['oferta_id'] ?? null;
        $target = $data['target'] ?? null;
        $subject = $data['subject'] ?? 'Notificação do Processo Seletivo';
        $body = $data['body'] ?? '';

        if (!$ofertaId || !$target || empty($subject) || empty($body)) {
            echo json_encode(['success' => false, 'message' => 'Dados insuficientes para enviar o email.']);
            exit;
        }

        $inscritos = AlunoOferta::getRelatorio($ofertaId, null);
        if (empty($inscritos)) {
            echo json_encode(['success' => false, 'message' => 'Nenhum aluno encontrado para esta oferta.']);
            exit;
        }

        $recipients = [];
        if ($target === 'Geral') {
            $recipients = array_column($inscritos, 'email', 'nome');
        } elseif ($target === 'Aprovado' || $target === 'Lista de Espera') {
            foreach ($inscritos as $inscrito) {
                if ($inscrito['status_selecao'] === $target) {
                    $recipients[$inscrito['nome']] = $inscrito['email'];
                }
            }
        } elseif (is_numeric($target)) { // Envio individual
            foreach ($inscritos as $inscrito) {
                if ($inscrito['alunos_id'] == $target) {
                    $recipients[$inscrito['nome']] = $inscrito['email'];
                    break;
                }
            }
        }

        if (empty($recipients)) {
            echo json_encode(['success' => false, 'message' => 'Nenhum destinatário encontrado para o critério: ' . htmlspecialchars($target)]);
            exit;
        }

        $mail = new PHPMailer(true);
        try {
            //Configurações do Servidor SMTP a partir do arquivo de configuração
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = MAIL_SMTP_AUTH;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_SMTP_SECURE;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            //Remetente
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

            //Adiciona todos os destinatários
            foreach ($recipients as $name => $email) {
                $mail->addAddress($email, $name);
            }

            //Conteúdo
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body); // Versão em texto puro

            $mail->send();
            echo json_encode(['success' => true, 'message' => count($recipients) . ' email(s) enviado(s) com sucesso!']);

        } catch (Exception $e) {
            // Retorna uma mensagem de erro mais detalhada para depuração
            echo json_encode(['success' => false, 'message' => "O email não pôde ser enviado. Erro: {$mail->ErrorInfo}"]);
        }
        exit;
    }

    public function vincularProfessor() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }
    
        $selectedOfertaId = $_GET['oferta_id'] ?? null;
    
        // Se for uma requisição AJAX para obter dados de uma turma
        if ($selectedOfertaId && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            try {
                $db = Database::getInstance()->getConnection();
                
                // 1. Buscar todos os professores disponíveis (tipo_usuario_id = 2)
                $stmtProf = $db->query("SELECT id, nome FROM usuarios WHERE tipo_usuario_id = 2 ORDER BY nome");
                $todosProfessores = $stmtProf->fetchAll(PDO::FETCH_ASSOC);

                // 2. Buscar professores já vinculados a esta turma
                $stmtVinculados = $db->prepare(
                    "SELECT p.id as vinculo_id, u.id as professor_id, u.nome 
                     FROM professor p 
                     JOIN usuarios u ON p.usuarios_id = u.id 
                     WHERE p.oferta_id = :oferta_id
                     ORDER BY u.nome"
                );
                $stmtVinculados->execute([':oferta_id' => $selectedOfertaId]);
                $professoresVinculados = $stmtVinculados->fetchAll(PDO::FETCH_ASSOC);

                // 3. Determinar os professores ainda não vinculados
                $idsVinculados = array_column($professoresVinculados, 'professor_id');
                $professoresDisponiveis = array_filter($todosProfessores, function($prof) use ($idsVinculados) {
                    return !in_array($prof['id'], $idsVinculados);
                });

                echo json_encode([
                    'success' => true,
                    'disponiveis' => array_values($professoresDisponiveis), // Reindexar array
                    'vinculados' => $professoresVinculados
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
            }
            exit;
        }
    
        // Se for carregamento normal da página, apenas carrega as ofertas
        $ofertas = Oferta::findAll();
        require_once __DIR__ . '/../views/layouts/vincular.php';
    }

    public function ajaxLinkProfessor() {
        header('Content-Type: application/json');
        if (!is_admin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        $ofertaId = $data['oferta_id'] ?? null;
        $professorId = $data['professor_id'] ?? null;
    
        if (!$ofertaId || !$professorId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Dados insuficientes.']);
            exit;
        }
    
        try {
            $db = Database::getInstance()->getConnection();

            // Verificar se o vínculo já existe para não duplicar
            $stmtCheck = $db->prepare("SELECT id FROM professor WHERE oferta_id = :oferta_id AND usuarios_id = :professor_id");
            $stmtCheck->execute([':oferta_id' => $ofertaId, ':professor_id' => $professorId]);
            if ($stmtCheck->fetch()) {
                echo json_encode(['success' => true, 'message' => 'Professor já vinculado.']);
                exit;
            }

            $stmt = $db->prepare("INSERT INTO professor (oferta_id, usuarios_id) VALUES (:oferta_id, :usuario_id)");
            $stmt->execute([':oferta_id' => $ofertaId, ':usuario_id' => $professorId]);
            $newVinculoId = $db->lastInsertId();
            
            echo json_encode(['success' => true, 'message' => 'Professor vinculado com sucesso!', 'vinculo_id' => $newVinculoId]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao vincular professor: ' . $e->getMessage()]);
        }
        exit;
    }

    public function ajaxUnlinkProfessor() {
        header('Content-Type: application/json');
        if (!is_admin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }
    
        $data = json_decode(file_get_contents('php://input'), true);
        $vinculoId = $data['vinculo_id'] ?? null;
    
        if (!$vinculoId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID do vínculo é necessário.']);
            exit;
        }
    
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM professor WHERE id = :vinculo_id");
            $stmt->execute([':vinculo_id' => $vinculoId]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Professor desvinculado com sucesso!']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Vínculo não encontrado.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao desvincular professor: ' . $e->getMessage()]);
        }
        exit;
    }

    public function visualizarInscritos() {
        if (!is_admin() && !is_professor()) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $query = "SELECT a.*, u.email as usuario_email, u.login as usuario_login, u.id as usuario_id FROM alunos a LEFT JOIN usuarios u ON a.usuarios_id = u.id ORDER BY a.nome ASC";
        $stmt = $db->query($query);
        $inscritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/dashboard/visualizar_inscritos.php';
    }

    public function getInscrito() {
        if (!is_admin() && !is_professor()) {
            http_response_code(403);
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID não fornecido']);
            exit;
        }
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT a.*, u.email as usuario_email, u.login as usuario_login, u.id as usuario_id FROM alunos a LEFT JOIN usuarios u ON a.usuarios_id = u.id WHERE a.id = :id");
        $stmt->execute(['id' => $id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($aluno) {
            echo json_encode($aluno);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Aluno não encontrado']);
        }
        exit;
    }

    public function updateInscrito() {
        if ((!is_admin() && !is_professor()) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }
        $id = $_POST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID não fornecido']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $db->beginTransaction();
        try {
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $whatsapp = $_POST['whatsapp'] ?? '';
            $telefone = $_POST['telefone'] ?? '';
            $login = $_POST['login'] ?? '';
            $senha = $_POST['senha'] ?? '';
            $usuario_id = $_POST['usuario_id'] ?? null;

            // Verifica se o login ou email já existe no banco de dados para outro usuário
            if ($usuario_id) {
                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE (email = :email OR login = :login) AND id != :id");
                $stmtCheck->execute(['email' => $email, 'login' => $login, 'id' => $usuario_id]);
                if ($stmtCheck->fetch()) {
                    echo json_encode(['error' => 'O login ou email informado já está sendo usado por outro usuário. Por favor, escolha outros.']);
                    exit;
                }
            }

            $stmt = $db->prepare("UPDATE alunos SET nome = :nome, email = :email, whatsapp = :whatsapp, telefone = :telefone WHERE id = :id");
            $stmt->execute([
                'nome' => $nome,
                'email' => $email,
                'whatsapp' => $whatsapp,
                'telefone' => $telefone,
                'id' => $id
            ]);

            if ($usuario_id && !empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmtU = $db->prepare("UPDATE usuarios SET senha = :senha, email = :email, login = :login WHERE id = :id");
                $stmtU->execute([
                    'senha' => $senhaHash,
                    'email' => $email,
                    'login' => $login,
                    'id' => $usuario_id
                ]);
            } else if ($usuario_id) {
                $stmtU = $db->prepare("UPDATE usuarios SET email = :email, login = :login WHERE id = :id");
                $stmtU->execute([
                    'email' => $email,
                    'login' => $login,
                    'id' => $usuario_id
                ]);
            }
            
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Aluno atualizado com sucesso!']);
        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
        exit;
    }

    public function vincularAluno() {
        if (!is_admin() && !is_professor()) {
            header('Location: index.php');
            exit;
        }

        $ofertas = Oferta::findAll();
        require_once __DIR__ . '/../views/dashboard/vincular_aluno.php';
    }

    public function ajaxBuscarAlunosParaVinculo() {
        if (!is_admin() && !is_professor()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit;
        }
        $oferta_id = $_GET['oferta_id'] ?? null;
        $busca = trim($_GET['busca'] ?? '');

        if (!$oferta_id) {
            echo json_encode(['success' => false, 'message' => 'Oferta não informada.']);
            exit;
        }

        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT a.*, u.email as usuario_email, u.login as usuario_login 
                FROM alunos a 
                LEFT JOIN usuarios u ON a.usuarios_id = u.id 
                WHERE a.id NOT IN (SELECT alunos_id FROM alunos_has_oferta WHERE oferta_id = :oferta_id)";
        
        $params = [':oferta_id' => $oferta_id];

        if (!empty($busca)) {
            $sql .= " AND (a.nome LIKE :busca OR u.email LIKE :busca OR u.login LIKE :busca)";
            $params[':busca'] = '%' . $busca . '%';
        }

        $sql .= " ORDER BY a.nome ASC LIMIT 50";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'alunos' => $alunos]);
        exit;
    }

    public function ajaxVincularAluno() {
        if ((!is_admin() && !is_professor()) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Não autorizado']);
            exit;
        }

        $aluno_id = $_POST['aluno_id'] ?? null;
        $oferta_id = $_POST['oferta_id'] ?? null;

        if (!$aluno_id || !$oferta_id) {
            echo json_encode(['success' => false, 'message' => 'Dados incompletos (Aluno ou Oferta faltando).']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();

            $sql = "INSERT INTO alunos_has_oferta (
                        alunos_id, oferta_id, status_selecao, termo_aceite,
                        exerce_atividade_remunerada, vinculo_atividade, afinidade_atividade_curso,
                        participacao_economica_familiar, numero_membros_familia, renda_familiar,
                        motivo_escolha_curso, o_que_mais_espera_do_curso, expectativas_relacao_curso,
                        expectativas_profissionais_pos_curso, canal_informacao_curso, turno_preferencia
                    ) VALUES (
                        :alunos_id, :oferta_id, 'Aprovado', :termo_aceite,
                        :exerce_atividade_remunerada, :vinculo_atividade, :afinidade_atividade_curso,
                        :participacao_economica_familiar, :numero_membros_familia, :renda_familiar,
                        :motivo_escolha_curso, :o_que_mais_espera_do_curso, :expectativas_relacao_curso,
                        :expectativas_profissionais_pos_curso, :canal_informacao_curso, :turno_preferencia
                    )";

            $stmt = $db->prepare($sql);
            
            // O formulário de vínculo não tem um checkbox de termo_aceite explícito no modal para o admin. 
            // O admin está preenchendo isso pelo aluno, logo, define-se 'Sim' implicitamente ou pegando do POST se existir.
            $termo_aceite = $_POST['termo_aceite'] ?? 'Sim';

            $stmt->execute([
                ':alunos_id' => $aluno_id,
                ':oferta_id' => $oferta_id,
                ':termo_aceite' => $termo_aceite,
                ':exerce_atividade_remunerada' => $_POST['exerce_atividade_remunerada'] ?? null,
                ':vinculo_atividade' => $_POST['vinculo_atividade'] ?? null,
                ':afinidade_atividade_curso' => $_POST['afinidade_atividade_curso'] ?? null,
                ':participacao_economica_familiar' => $_POST['participacao_economica_familiar'] ?? null,
                ':numero_membros_familia' => $_POST['numero_membros_familia'] ?? null,
                ':renda_familiar' => $_POST['renda_familiar'] ?? null,
                ':motivo_escolha_curso' => $_POST['motivo_escolha_curso'] ?? null,
                ':o_que_mais_espera_do_curso' => $_POST['o_que_mais_espera_do_curso'] ?? null,
                ':expectativas_relacao_curso' => $_POST['expectativas_relacao_curso'] ?? null,
                ':expectativas_profissionais_pos_curso' => $_POST['expectativas_profissionais_pos_curso'] ?? null,
                ':canal_informacao_curso' => $_POST['canal_informacao_curso'] ?? null,
                ':turno_preferencia' => $_POST['turno_preferencia'] ?? null
            ]);

            echo json_encode(['success' => true, 'message' => 'Aluno vinculado e aprovado com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao vincular: ' . $e->getMessage()]);
        }
        exit;
    }
}
