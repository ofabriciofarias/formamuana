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
}
