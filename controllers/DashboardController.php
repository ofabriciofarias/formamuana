<?php

require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/GeneticAlgorithm.php';
require_once __DIR__ . '/../core/IvsCalculator.php';
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/AlunoOferta.php';

// Importa as classes do PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class DashboardController {

    public function index() {
        if (!is_admin() && !is_professor()) {
            header('Location: ' . BASE_URL . 'index.php?action=login');
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
        $redirectTo = $_POST['redirect_to'] ?? 'relatorio_geral'; // Default para o comportamento antigo
        
        // Redirecionamento mantendo os filtros
        $redirectUrl = "index.php?action=" . $redirectTo . "&oferta_id=" . ($_POST['current_oferta_id'] ?? '') . "&busca=" . ($_POST['current_busca'] ?? '');

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

    public function vincularCertificados(){
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $ofertaId = $_GET['oferta_id'] ?? null;
        $ofertas = Oferta::findAll();
        $inscritos = [];
        $inscricoesEncerradas = true;

        if ($ofertaId) {
            $db = Database::getInstance()->getConnection();
            $query = "SELECT a.*, u.email as usuario_email, aho.status_selecao, aho.nota_final, aho.url_certificado, aho.concluiu_curso, aho.codigo_validacao, aho.oferta_id
                      FROM alunos a
                      JOIN usuarios u ON a.usuarios_id = u.id
                      JOIN alunos_has_oferta aho ON a.id = aho.alunos_id
                      WHERE aho.oferta_id = :oferta_id AND aho.status_selecao = 'Aprovado'
                      ORDER BY a.nome ASC";
            $stmt = $db->prepare($query);
            $stmt->execute([':oferta_id' => $ofertaId]);
            $inscritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcula frequencia
            $stmtDatas = $db->prepare("SELECT DISTINCT data_aula FROM aula WHERE oferta_id = :oferta_id");
            $stmtDatas->execute([':oferta_id' => $ofertaId]);
            $datas_aulas = $stmtDatas->fetchAll(PDO::FETCH_COLUMN);
            $total_dias_aula = count($datas_aulas);

            $stmtFreq = $db->prepare(
                "SELECT lf.alunos_id, a.data_aula
                 FROM lista_frequencia lf
                 JOIN aula a ON lf.aula_id = a.id
                 WHERE a.oferta_id = :oferta_id AND lf.presenca = 1"
            );
            $stmtFreq->execute([':oferta_id' => $ofertaId]);
            $frequenciasRaw = $stmtFreq->fetchAll(PDO::FETCH_ASSOC);

            $frequencias = [];
            foreach ($frequenciasRaw as $freq) {
                $frequencias[$freq['alunos_id']][$freq['data_aula']] = true;
            }

            foreach ($inscritos as &$inscrito) {
                $dias_presente = isset($frequencias[$inscrito['id']]) ? count($frequencias[$inscrito['id']]) : 0;
                $inscrito['frequencia_percentual'] = $total_dias_aula > 0 ? round(($dias_presente / $total_dias_aula) * 100) : 0;
            }
            unset($inscrito);
        }

        require_once __DIR__ . '/../views/dashboard/vincular_certificados.php';
    }

    public function uploadCertificado() {
        header('Content-Type: application/json');
        if (!is_admin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Acesso negado']);
            exit;
        }

        $alunoId = $_POST['aluno_id'] ?? null;
        $ofertaId = $_POST['oferta_id'] ?? null;
        $codigoValidacao = $_POST['codigo_validacao'] ?? null;
        $concluiuCurso = isset($_POST['concluiu_curso']) ? 1 : 0;
        
        $db = Database::getInstance()->getConnection();
        
        // Verifica frequencia e nota
        $stmtNota = $db->prepare("SELECT nota_final FROM alunos_has_oferta WHERE alunos_id = :aluno_id AND oferta_id = :oferta_id");
        $stmtNota->execute([':aluno_id' => $alunoId, ':oferta_id' => $ofertaId]);
        $notaFinal = $stmtNota->fetchColumn();

        $stmtDatas = $db->prepare("SELECT DISTINCT data_aula FROM aula WHERE oferta_id = :oferta_id");
        $stmtDatas->execute([':oferta_id' => $ofertaId]);
        $datas_aulas = $stmtDatas->fetchAll(PDO::FETCH_COLUMN);
        $total_dias_aula = count($datas_aulas);

        $stmtFreq = $db->prepare("SELECT COUNT(*) FROM lista_frequencia lf JOIN aula a ON lf.aula_id = a.id WHERE a.oferta_id = :oferta_id AND lf.alunos_id = :aluno_id AND lf.presenca = 1");
        $stmtFreq->execute([':oferta_id' => $ofertaId, ':aluno_id' => $alunoId]);
        $dias_presente = $stmtFreq->fetchColumn();

        $frequenciaPercentual = $total_dias_aula > 0 ? round(($dias_presente / $total_dias_aula) * 100) : 0;

        if ($frequenciaPercentual > 75 && $notaFinal > 5) {
            $urlCertificado = null;
            if ($concluiuCurso == 1 && isset($_FILES['certificado']) && $_FILES['certificado']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../certificados/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = uniqid() . '_' . basename($_FILES['certificado']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['certificado']['tmp_name'], $uploadFile)) {
                    $urlCertificado = 'certificados/' . $fileName;
                }
            }

            if ($urlCertificado) {
                $stmtUpdate = $db->prepare("UPDATE alunos_has_oferta SET concluiu_curso = :concluiu_curso, url_certificado = :url_certificado, codigo_validacao = :codigo_validacao WHERE alunos_id = :aluno_id AND oferta_id = :oferta_id");
                $stmtUpdate->execute([
                    ':concluiu_curso' => $concluiuCurso,
                    ':url_certificado' => $urlCertificado,
                    ':codigo_validacao' => $codigoValidacao,
                    ':aluno_id' => $alunoId,
                    ':oferta_id' => $ofertaId
                ]);
            } else {
                $stmtUpdate = $db->prepare("UPDATE alunos_has_oferta SET concluiu_curso = :concluiu_curso, codigo_validacao = :codigo_validacao WHERE alunos_id = :aluno_id AND oferta_id = :oferta_id");
                $stmtUpdate->execute([
                    ':concluiu_curso' => $concluiuCurso,
                    ':codigo_validacao' => $codigoValidacao,
                    ':aluno_id' => $alunoId,
                    ':oferta_id' => $ofertaId
                ]);
            }
            
            echo json_encode(['success' => true, 'message' => 'Certificado vinculado com sucesso!']);
            exit;
        }
        
        echo json_encode(['success' => false, 'message' => 'Requisitos não atendidos (Freq > 75% e Nota > 5).']);
        exit;
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
        $offset = isset($data['offset']) ? (int)$data['offset'] : 0;
        $limit = isset($data['limit']) ? (int)$data['limit'] : 49;

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
        
        $totalRecipients = count($recipients);
        if ($offset >= $totalRecipients) {
             echo json_encode(['success' => true, 'message' => 'Todos os emails foram enviados.', 'has_more' => false, 'total_count' => $totalRecipients]);
             exit;
        }

        $recipientsBatch = array_slice($recipients, $offset, $limit, true);
        $hasMore = ($offset + count($recipientsBatch)) < $totalRecipients;

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

            //Adiciona todos os destinatários do bloco
            foreach ($recipientsBatch as $name => $email) {
                if (count($recipientsBatch) > 1) {
                    $mail->addBCC($email, $name);
                } else {
                    $mail->addAddress($email, $name);
                }
            }
            
            if (count($recipientsBatch) > 1) {
                $mail->addAddress(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            }

            //Conteúdo
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body); // Versão em texto puro

            $mail->send();
            
            echo json_encode([
                'success' => true, 
                'message' => count($recipientsBatch) . ' email(s) enviado(s) com sucesso!',
                'sent_count' => count($recipientsBatch),
                'has_more' => $hasMore,
                'next_offset' => $offset + count($recipientsBatch),
                'total_count' => $totalRecipients
            ]);

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

    /**
     * Relatório de Vulnerabilidade Social e Econômica (IVS - Modelo IPEA)
     * Estratificado por Geral, Curso e Turma, com análise de alcance, lacuna,
     * zona rural/urbana e justiça distributiva da seleção.
     */
    public function relatorioVulnerabilidade() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance()->getConnection();

        // Filtros de navegabilidade
        $filtros = [
            'curso'     => trim($_GET['curso'] ?? ''),
            'oferta_id' => trim($_GET['oferta_id'] ?? ''),
            'dimensao'  => $_GET['dimensao'] ?? 'ivs_geral',
            'faixa'     => $_GET['faixa'] ?? 'todos',
            'zona'      => $_GET['zona'] ?? 'todos',
            'status'    => $_GET['status'] ?? 'todos',
            'cidade'    => trim($_GET['cidade'] ?? ''),
            'aba'       => $_GET['aba'] ?? 'geral',
        ];

        $dimensoesValidas = ['ivs_geral', 'ivs_infra', 'ivs_renda', 'ivs_capital'];
        if (!in_array($filtros['dimensao'], $dimensoesValidas, true)) {
            $filtros['dimensao'] = 'ivs_geral';
        }
        $dimKey = $filtros['dimensao'];
        $faixaKey = 'faixa_' . str_replace('ivs_', '', $dimKey); // faixa_geral, faixa_infra, faixa_renda, faixa_capital

        // Ofertas disponíveis para filtros
        $ofertas = $db->query("SELECT id, nome, turno, local, numero_vagas FROM oferta ORDER BY nome ASC, turno ASC")->fetchAll(PDO::FETCH_ASSOC);
        $cursosUnicos = array_values(array_unique(array_column($ofertas, 'nome')));

        // 1. Buscar todas as inscrições com dados completos de aluno e oferta
        $sqlInscricoes = "
            SELECT
                aho.alunos_id,
                aho.oferta_id,
                aho.status_selecao,
                aho.data_insc,
                aho.exerce_atividade_remunerada,
                aho.participacao_economica_familiar,
                aho.numero_membros_familia,
                aho.renda_familiar,
                a.nome AS aluno_nome,
                a.email,
                a.whatsapp,
                a.zona_residencia,
                a.cidade_origem,
                a.cidade_atual,
                a.bairro_comunidade,
                a.comunidade_tradicional,
                a.cor_raca,
                a.possui_deficiencia,
                a.possui_filhos,
                a.quantidade_filhos,
                a.tipo_escola_ensino_medio,
                a.nivel_escolaridade,
                a.beneficiario_programa_social,
                a.acesso_internet,
                a.dispositivo_principal,
                o.nome AS curso_nome,
                o.turno AS oferta_turno,
                o.local AS oferta_local,
                o.numero_vagas
            FROM alunos_has_oferta aho
            INNER JOIN alunos a ON aho.alunos_id = a.id
            INNER JOIN oferta o ON aho.oferta_id = o.id
            ORDER BY o.nome ASC, a.nome ASC
        ";
        $inscricoesRaw = $db->query($sqlInscricoes)->fetchAll(PDO::FETCH_ASSOC);

        // Coletar cidades disponíveis para filtro
        $cidadesSet = [];
        $registrosEnriquecidos = [];
        $inscricoesPorAluno = [];

        foreach ($inscricoesRaw as $row) {
            $ivs = IvsCalculator::calculate($row);
            $zonaNorm = IvsCalculator::normalizeZona($row['zona_residencia'] ?? '');
            $alcancado = IvsCalculator::isAlcancado($row['status_selecao'] ?? '');
            $cidadeRef = trim((string)($row['cidade_origem'] ?: ($row['cidade_atual'] ?? '')));
            if ($cidadeRef !== '') {
                $cidadesSet[$cidadeRef] = $cidadeRef;
            }

            $item = array_merge($row, $ivs, [
                'zona_norm' => $zonaNorm,
                'alcancado' => $alcancado,
                'cidade_ref' => $cidadeRef !== '' ? $cidadeRef : 'Não Informada',
            ]);

            $registrosEnriquecidos[] = $item;
            $inscricoesPorAluno[$row['alunos_id']][] = $item;
        }

        ksort($cidadesSet);
        $cidadesDisponiveis = array_values($cidadesSet);

        // 2. Filtrar registros conforme seleção do usuário
        $registrosFiltrados = array_values(array_filter($registrosEnriquecidos, function ($item) use ($filtros, $faixaKey) {
            if ($filtros['curso'] !== '' && $item['curso_nome'] !== $filtros['curso']) {
                return false;
            }
            if ($filtros['oferta_id'] !== '' && (string)$item['oferta_id'] !== (string)$filtros['oferta_id']) {
                return false;
            }
            if ($filtros['zona'] !== 'todos' && $item['zona_norm'] !== $filtros['zona']) {
                return false;
            }
            if ($filtros['status'] === 'alcancado' && !$item['alcancado']) {
                return false;
            }
            if ($filtros['status'] === 'lacuna' && $item['alcancado']) {
                return false;
            }
            if ($filtros['cidade'] !== '' && $item['cidade_ref'] !== $filtros['cidade']) {
                return false;
            }
            if ($filtros['faixa'] !== 'todos') {
                $faixaItem = $item[$faixaKey];
                if ($filtros['faixa'] === 'vulneravel') {
                    if (!$faixaItem['vulnerable']) {
                        return false;
                    }
                } elseif ($faixaItem['slug'] !== $filtros['faixa']) {
                    return false;
                }
            }
            return true;
        }));

        // Helper para inicializar acumulador de métricas
        $initBucket = function (string $label, array $meta = []): array {
            return array_merge([
                'label' => $label,
                'total' => 0,
                'alcancados' => 0,
                'lacuna' => 0,
                'vuln_total' => 0,
                'vuln_alcancados' => 0,
                'vuln_lacuna' => 0,
                'rural_total' => 0,
                'rural_alcancados' => 0,
                'rural_lacuna' => 0,
                'urbana_total' => 0,
                'urbana_alcancados' => 0,
                'urbana_lacuna' => 0,
                'soma_ivs_geral' => 0.0,
                'soma_ivs_infra' => 0.0,
                'soma_ivs_renda' => 0.0,
                'soma_ivs_capital' => 0.0,
                'soma_dim_alcancados' => 0.0,
                'soma_dim_lacuna' => 0.0,
                'soma_geral_alcancados' => 0.0,
                'soma_geral_lacuna' => 0.0,
                'soma_infra_alcancados' => 0.0,
                'soma_infra_lacuna' => 0.0,
                'soma_renda_alcancados' => 0.0,
                'soma_renda_lacuna' => 0.0,
                'soma_capital_alcancados' => 0.0,
                'soma_capital_lacuna' => 0.0,
            ], $meta);
        };

        // Helper para acumular um registro num bucket
        $accumulate = function (array &$bucket, array $item) use ($dimKey, $faixaKey): void {
            $isAlc = $item['alcancado'];
            $isVuln = $item[$faixaKey]['vulnerable'];

            $bucket['total']++;
            if ($isAlc) {
                $bucket['alcancados']++;
                $bucket['soma_dim_alcancados'] += $item[$dimKey];
                $bucket['soma_geral_alcancados'] += $item['ivs_geral'];
                $bucket['soma_infra_alcancados'] += $item['ivs_infra'];
                $bucket['soma_renda_alcancados'] += $item['ivs_renda'];
                $bucket['soma_capital_alcancados'] += $item['ivs_capital'];
            } else {
                $bucket['lacuna']++;
                $bucket['soma_dim_lacuna'] += $item[$dimKey];
                $bucket['soma_geral_lacuna'] += $item['ivs_geral'];
                $bucket['soma_infra_lacuna'] += $item['ivs_infra'];
                $bucket['soma_renda_lacuna'] += $item['ivs_renda'];
                $bucket['soma_capital_lacuna'] += $item['ivs_capital'];
            }

            if ($isVuln) {
                $bucket['vuln_total']++;
                if ($isAlc) {
                    $bucket['vuln_alcancados']++;
                } else {
                    $bucket['vuln_lacuna']++;
                }
            }

            if ($item['zona_norm'] === 'Rural') {
                $bucket['rural_total']++;
                if ($isAlc) {
                    $bucket['rural_alcancados']++;
                } else {
                    $bucket['rural_lacuna']++;
                }
            } elseif ($item['zona_norm'] === 'Urbana') {
                $bucket['urbana_total']++;
                if ($isAlc) {
                    $bucket['urbana_alcancados']++;
                } else {
                    $bucket['urbana_lacuna']++;
                }
            }

            $bucket['soma_ivs_geral'] += $item['ivs_geral'];
            $bucket['soma_ivs_infra'] += $item['ivs_infra'];
            $bucket['soma_ivs_renda'] += $item['ivs_renda'];
            $bucket['soma_ivs_capital'] += $item['ivs_capital'];
        };

        // Helper para finalizar médias e taxas do bucket
        $finalizeBucket = function (array $b): array {
            $b['taxa_alcance_geral'] = $b['total'] > 0 ? round(($b['alcancados'] / $b['total']) * 100, 1) : 0.0;
            $b['taxa_alcance_vuln'] = $b['vuln_total'] > 0 ? round(($b['vuln_alcancados'] / $b['vuln_total']) * 100, 1) : 0.0;
            $b['taxa_alcance_rural'] = $b['rural_total'] > 0 ? round(($b['rural_alcancados'] / $b['rural_total']) * 100, 1) : 0.0;
            $b['taxa_alcance_urbana'] = $b['urbana_total'] > 0 ? round(($b['urbana_alcancados'] / $b['urbana_total']) * 100, 1) : 0.0;

            $b['media_ivs_geral'] = $b['total'] > 0 ? round($b['soma_ivs_geral'] / $b['total'], 3) : 0.0;
            $b['media_ivs_infra'] = $b['total'] > 0 ? round($b['soma_ivs_infra'] / $b['total'], 3) : 0.0;
            $b['media_ivs_renda'] = $b['total'] > 0 ? round($b['soma_ivs_renda'] / $b['total'], 3) : 0.0;
            $b['media_ivs_capital'] = $b['total'] > 0 ? round($b['soma_ivs_capital'] / $b['total'], 3) : 0.0;

            $b['media_dim_alcancados'] = $b['alcancados'] > 0 ? round($b['soma_dim_alcancados'] / $b['alcancados'], 3) : 0.0;
            $b['media_dim_lacuna'] = $b['lacuna'] > 0 ? round($b['soma_dim_lacuna'] / $b['lacuna'], 3) : 0.0;

            $b['media_geral_alcancados'] = $b['alcancados'] > 0 ? round($b['soma_geral_alcancados'] / $b['alcancados'], 3) : 0.0;
            $b['media_geral_lacuna'] = $b['lacuna'] > 0 ? round($b['soma_geral_lacuna'] / $b['lacuna'], 3) : 0.0;
            $b['media_infra_alcancados'] = $b['alcancados'] > 0 ? round($b['soma_infra_alcancados'] / $b['alcancados'], 3) : 0.0;
            $b['media_infra_lacuna'] = $b['lacuna'] > 0 ? round($b['soma_infra_lacuna'] / $b['lacuna'], 3) : 0.0;
            $b['media_renda_alcancados'] = $b['alcancados'] > 0 ? round($b['soma_renda_alcancados'] / $b['alcancados'], 3) : 0.0;
            $b['media_renda_lacuna'] = $b['lacuna'] > 0 ? round($b['soma_renda_lacuna'] / $b['lacuna'], 3) : 0.0;
            $b['media_capital_alcancados'] = $b['alcancados'] > 0 ? round($b['soma_capital_alcancados'] / $b['alcancados'], 3) : 0.0;
            $b['media_capital_lacuna'] = $b['lacuna'] > 0 ? round($b['soma_capital_lacuna'] / $b['lacuna'], 3) : 0.0;

            // Índice de Justiça Distributiva:
            // Diferença entre IVS médio dos aprovados e IVS médio dos não aprovados (positivo = priorizou mais vulneráveis)
            $b['delta_justica'] = round($b['media_dim_alcancados'] - $b['media_dim_lacuna'], 3);
            $pctVulnAprov = $b['alcancados'] > 0 ? ($b['vuln_alcancados'] / $b['alcancados']) : 0.0;
            $pctVulnTotal = $b['total'] > 0 ? ($b['vuln_total'] / $b['total']) : 0.0;
            $b['razao_prioridade'] = $pctVulnTotal > 0 ? round($pctVulnAprov / $pctVulnTotal, 2) : 0.0;

            return $b;
        };

        // 3. Estratificação: GERAL (Inscrições), POR CURSO e POR TURMA
        $geralBucket = $initBucket('Geral (Inscrições)');
        $cursosBuckets = [];
        $turmasBuckets = [];

        // Pré-popular turmas com vagas conhecidas (respeitando filtro de curso/oferta se ativo)
        foreach ($ofertas as $of) {
            if ($filtros['curso'] !== '' && $of['nome'] !== $filtros['curso']) {
                continue;
            }
            if ($filtros['oferta_id'] !== '' && (string)$of['id'] !== (string)$filtros['oferta_id']) {
                continue;
            }
            $turmaId = $of['id'];
            $turmasBuckets[$turmaId] = $initBucket($of['nome'], [
                'oferta_id' => $turmaId,
                'turno' => $of['turno'] ?? 'N/I',
                'local' => $of['local'] ?? 'N/I',
                'vagas' => (int)($of['numero_vagas'] ?? 0),
            ]);
            if (!isset($cursosBuckets[$of['nome']])) {
                $cursosBuckets[$of['nome']] = $initBucket($of['nome'], ['vagas' => 0, 'qtd_turmas' => 0]);
            }
            $cursosBuckets[$of['nome']]['vagas'] += (int)($of['numero_vagas'] ?? 0);
            $cursosBuckets[$of['nome']]['qtd_turmas']++;
        }

        // Faixas IPEA e Resumo das 4 Dimensões
        $faixasIpea = [
            'muito_baixa' => ['label' => 'Muito Baixa (0,000–0,200)', 'alcancados' => 0, 'lacuna' => 0, 'color' => '#2ecc71'],
            'baixa'       => ['label' => 'Baixa (0,201–0,300)',       'alcancados' => 0, 'lacuna' => 0, 'color' => '#27ae60'],
            'media'       => ['label' => 'Média (0,301–0,400)',       'alcancados' => 0, 'lacuna' => 0, 'color' => '#f39c12'],
            'alta'        => ['label' => 'Alta (0,401–0,500)',        'alcancados' => 0, 'lacuna' => 0, 'color' => '#e67e22'],
            'muito_alta'  => ['label' => 'Muito Alta (0,501–1,000)',  'alcancados' => 0, 'lacuna' => 0, 'color' => '#e74c3c'],
        ];

        $dimensoesResumo = [
            'ivs_geral'   => ['nome' => 'IVS Geral (Composto)',       'vuln_total' => 0, 'vuln_alcancados' => 0, 'vuln_lacuna' => 0, 'soma_alc' => 0.0, 'soma_lac' => 0.0],
            'ivs_infra'   => ['nome' => 'IVS Infraestrutura Urbana',  'vuln_total' => 0, 'vuln_alcancados' => 0, 'vuln_lacuna' => 0, 'soma_alc' => 0.0, 'soma_lac' => 0.0],
            'ivs_renda'   => ['nome' => 'IVS Renda e Trabalho',       'vuln_total' => 0, 'vuln_alcancados' => 0, 'vuln_lacuna' => 0, 'soma_alc' => 0.0, 'soma_lac' => 0.0],
            'ivs_capital' => ['nome' => 'IVS Capital Humano',         'vuln_total' => 0, 'vuln_alcancados' => 0, 'vuln_lacuna' => 0, 'soma_alc' => 0.0, 'soma_lac' => 0.0],
        ];

        $cidadesBuckets = [];

        foreach ($registrosFiltrados as $item) {
            $accumulate($geralBucket, $item);

            $cName = $item['curso_nome'];
            if (!isset($cursosBuckets[$cName])) {
                $cursosBuckets[$cName] = $initBucket($cName, ['vagas' => 0, 'qtd_turmas' => 0]);
            }
            $accumulate($cursosBuckets[$cName], $item);

            $tId = $item['oferta_id'];
            if (!isset($turmasBuckets[$tId])) {
                $turmasBuckets[$tId] = $initBucket($cName, [
                    'oferta_id' => $tId,
                    'turno' => $item['oferta_turno'] ?? 'N/I',
                    'local' => $item['oferta_local'] ?? 'N/I',
                    'vagas' => (int)($item['numero_vagas'] ?? 0),
                ]);
            }
            $accumulate($turmasBuckets[$tId], $item);

            // Faixas IPEA para a dimensão selecionada
            $slug = $item[$faixaKey]['slug'];
            if (isset($faixasIpea[$slug])) {
                if ($item['alcancado']) {
                    $faixasIpea[$slug]['alcancados']++;
                } else {
                    $faixasIpea[$slug]['lacuna']++;
                }
            }

            // Resumo de todas as 4 dimensões simultaneamente
            foreach (['ivs_geral' => 'faixa_geral', 'ivs_infra' => 'faixa_infra', 'ivs_renda' => 'faixa_renda', 'ivs_capital' => 'faixa_capital'] as $dKey => $fKey) {
                if ($item['alcancado']) {
                    $dimensoesResumo[$dKey]['soma_alc'] += $item[$dKey];
                } else {
                    $dimensoesResumo[$dKey]['soma_lac'] += $item[$dKey];
                }
                if ($item[$fKey]['vulnerable']) {
                    $dimensoesResumo[$dKey]['vuln_total']++;
                    if ($item['alcancado']) {
                        $dimensoesResumo[$dKey]['vuln_alcancados']++;
                    } else {
                        $dimensoesResumo[$dKey]['vuln_lacuna']++;
                    }
                }
            }

            // Cidades
            $cid = $item['cidade_ref'];
            if (!isset($cidadesBuckets[$cid])) {
                $cidadesBuckets[$cid] = $initBucket($cid);
            }
            $accumulate($cidadesBuckets[$cid], $item);
        }

        $geralInscricoes = $finalizeBucket($geralBucket);
        $porCurso = array_map($finalizeBucket, array_values($cursosBuckets));
        $porTurma = array_map($finalizeBucket, array_values($turmasBuckets));
        $porCidade = array_map($finalizeBucket, array_values($cidadesBuckets));
        usort($porCidade, fn($a, $b) => $b['total'] <=> $a['total']);

        foreach ($dimensoesResumo as $k => &$d) {
            $d['taxa_alcance'] = $d['vuln_total'] > 0 ? round(($d['vuln_alcancados'] / $d['vuln_total']) * 100, 1) : 0.0;
            $d['media_alc'] = $geralInscricoes['alcancados'] > 0 ? round($d['soma_alc'] / $geralInscricoes['alcancados'], 3) : 0.0;
            $d['media_lac'] = $geralInscricoes['lacuna'] > 0 ? round($d['soma_lac'] / $geralInscricoes['lacuna'], 3) : 0.0;
            $d['delta_justica'] = round($d['media_alc'] - $d['media_lac'], 3);
        }
        unset($d);

        // 4. Visão Global de Alunos Únicos (Base `alunos` inteira, inclusive não inscritos)
        $todosAlunos = $db->query("
            SELECT id, nome, zona_residencia, cidade_origem, cidade_atual, comunidade_tradicional,
                   possui_deficiencia, possui_filhos, quantidade_filhos, tipo_escola_ensino_medio,
                   nivel_escolaridade, beneficiario_programa_social, acesso_internet, dispositivo_principal
            FROM alunos
        ")->fetchAll(PDO::FETCH_ASSOC);

        $globalAlunosBucket = $initBucket('Base Global (Alunos Únicos)');
        $globalSemInscricao = 0;
        $globalVulnSemInscricao = 0;

        foreach ($todosAlunos as $al) {
            $inscs = $inscricoesPorAluno[$al['id']] ?? [];
            $alcancadoGlobal = false;
            $melhorReg = $al;

            if (!empty($inscs)) {
                // Usa dados socioeconômicos da inscrição mais recente/vulnerável
                $melhorReg = $inscs[0];
                foreach ($inscs as $ins) {
                    if ($ins['alcancado']) {
                        $alcancadoGlobal = true;
                        $melhorReg = $ins;
                        break;
                    }
                }
            } else {
                $globalSemInscricao++;
            }

            $ivsAl = IvsCalculator::calculate($melhorReg);
            $zonaAl = IvsCalculator::normalizeZona($melhorReg['zona_residencia'] ?? '');
            $itemGlobal = array_merge($melhorReg, $ivsAl, [
                'zona_norm' => $zonaAl,
                'alcancado' => $alcancadoGlobal,
            ]);

            if (empty($inscs) && $itemGlobal[$faixaKey]['vulnerable']) {
                $globalVulnSemInscricao++;
            }

            $accumulate($globalAlunosBucket, $itemGlobal);
        }
        $geralAlunosUnicos = $finalizeBucket($globalAlunosBucket);
        $geralAlunosUnicos['sem_inscricao'] = $globalSemInscricao;
        $geralAlunosUnicos['vuln_sem_inscricao'] = $globalVulnSemInscricao;

        require_once __DIR__ . '/../views/dashboard/relatorio_vulnerabilidade.php';
    }
}
