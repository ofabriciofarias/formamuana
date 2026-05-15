<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/Aluno.php';
require_once __DIR__ . '/../models/AlunoOferta.php';

class ProfessorController {

    public function __construct() {
        // A verificação é movida para dentro de cada método para permitir
        // que a sessão seja iniciada corretamente pelo helper.
    }

    public function dashboard() {
        if (!is_professor()) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare(
            "SELECT o.* FROM oferta o 
             JOIN professor p ON o.id = p.oferta_id 
             WHERE p.usuarios_id = :professor_id 
             ORDER BY o.nome"
        );
        $stmt->execute([':professor_id' => $_SESSION['user_id']]);
        $turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/dashboard.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function lancarFrequencia() {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $db = Database::getInstance()->getConnection();
        
        $oferta = Oferta::findById($ofertaId);
        
        $stmtAlunos = $db->prepare(
            "SELECT a.id, a.nome FROM alunos a
             JOIN alunos_has_oferta aho ON a.id = aho.alunos_id
             WHERE aho.oferta_id = :oferta_id AND aho.status_selecao = 'Aprovado'
             ORDER BY a.nome"
        );
        $stmtAlunos->execute([':oferta_id' => $ofertaId]);
        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

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

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/frequencia.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }
    
    public function consultarFrequencia() {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $db = Database::getInstance()->getConnection();
        
        $oferta = Oferta::findById($ofertaId);
        
        $stmtAlunos = $db->prepare(
            "SELECT a.id, a.nome FROM alunos a
             JOIN alunos_has_oferta aho ON a.id = aho.alunos_id
             WHERE aho.oferta_id = :oferta_id AND aho.status_selecao = 'Aprovado'
             ORDER BY a.nome"
        );
        $stmtAlunos->execute([':oferta_id' => $ofertaId]);
        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

        // Busca todas as datas de aula distintas para esta oferta
        $stmtDatas = $db->prepare(
            "SELECT DISTINCT data_aula FROM aula 
             WHERE oferta_id = :oferta_id 
             ORDER BY data_aula ASC"
        );
        $stmtDatas->execute([':oferta_id' => $ofertaId]);
        $datas_aulas = $stmtDatas->fetchAll(PDO::FETCH_COLUMN);

        // Busca todos os registros de presença para a oferta
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

        // Calcula a frequência em percentual para cada aluno
        $total_dias_aula = count($datas_aulas);
        $frequencia_percentual = [];
        if ($total_dias_aula > 0) {
            foreach ($alunos as $aluno) {
                $dias_presente = isset($frequencias[$aluno['id']]) ? count($frequencias[$aluno['id']]) : 0;
                $percentual = ($dias_presente / $total_dias_aula) * 100;
                $frequencia_percentual[$aluno['id']] = round($percentual);
            }
        }

        $isReadOnly = true;

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/frequencia.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function salvarFrequencia() {
        if (!is_professor()) { http_response_code(403); echo json_encode(['success' => false, 'message' => 'Acesso negado.']); exit; }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $ofertaId = $data['ofertaId'] ?? null;
        $alunoId = $data['alunoId'] ?? null;
        $dataAula = $data['dataAula'] ?? null;
        $presente = $data['presente'] ?? false;

        if (!$this->checkAccess($ofertaId) || !$alunoId || !$dataAula) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Dados inválidos ou acesso negado.']);
            exit;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            $stmtAula = $db->prepare("SELECT id FROM aula WHERE oferta_id = :oferta_id AND data_aula = :data_aula");
            $stmtAula->execute([':oferta_id' => $ofertaId, ':data_aula' => $dataAula]);
            $aula = $stmtAula->fetch(PDO::FETCH_ASSOC);

            if (!$aula) {
                $stmtInsertAula = $db->prepare("INSERT INTO aula (oferta_id, data_aula) VALUES (:oferta_id, :data_aula)");
                $stmtInsertAula->execute([':oferta_id' => $ofertaId, ':data_aula' => $dataAula]);
                $aulaId = $db->lastInsertId();
            } else {
                $aulaId = $aula['id'];
            }

            $stmtDelete = $db->prepare("DELETE FROM lista_frequencia WHERE aula_id = :aula_id AND alunos_id = :aluno_id");
            $stmtDelete->execute([':aula_id' => $aulaId, ':aluno_id' => $alunoId]);

            if ($presente) {
                $stmtInsertFreq = $db->prepare("INSERT INTO lista_frequencia (aula_id, alunos_id, presenca) VALUES (:aula_id, :aluno_id, 1)");
                $stmtInsertFreq->execute([':aula_id' => $aulaId, ':aluno_id' => $alunoId]);
            }

            $db->commit();
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            $db->rollBack();
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function notas() {
        if (!is_professor()) { header('Location: index.php'); exit; }

        $ofertaId = $_GET['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }

        $db = Database::getInstance()->getConnection();
        
        $oferta = Oferta::findById($ofertaId);
        
        $stmtAlunos = $db->prepare(
            "SELECT a.id, a.nome, aho.nota_final FROM alunos a
             JOIN alunos_has_oferta aho ON a.id = aho.alunos_id
             WHERE aho.oferta_id = :oferta_id AND aho.status_selecao = 'Aprovado'
             ORDER BY a.nome"
        );
        $stmtAlunos->execute([':oferta_id' => $ofertaId]);
        $alunos = $stmtAlunos->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/layouts/professor_header.php';
        require_once __DIR__ . '/../views/professor/notas.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function salvarNotas() {
        if (!is_professor()) {
            header('Location: index.php');
            exit;
        }
    
        $ofertaId = $_POST['oferta_id'] ?? null;
        if (!$this->checkAccess($ofertaId)) {
            die('Acesso negado a esta turma.');
        }
    
        $notas = $_POST['notas'] ?? [];
    
        $db = Database::getInstance()->getConnection();
    
        foreach ($notas as $alunoId => $nota) {
            $notaStr = trim($nota);
            if ($notaStr === '') {
                continue; // Permite o lançamento parcelado ignorando campos vazios
            }

            // Garante que a nota seja um valor numérico e esteja no intervalo correto
            $notaFinal = (float)str_replace(',', '.', $notaStr);
    
            if ($notaFinal < 0 || $notaFinal > 10) {
                // Poderíamos adicionar uma mensagem de erro aqui
                continue; // Pula para o próximo aluno se a nota for inválida
            }
    
            $concluiuCurso = ($notaFinal >= 5) ? 1 : 0;
    
            // Prepara a query para atualizar a nota e o status de conclusão
            $stmt = $db->prepare(
                "UPDATE alunos_has_oferta 
                 SET nota_final = :nota_final, concluiu_curso = :concluiu_curso
                 WHERE alunos_id = :aluno_id AND oferta_id = :oferta_id"
            );
    
            $stmt->bindValue(':nota_final', $notaFinal, PDO::PARAM_STR);
            $stmt->bindValue(':concluiu_curso', $concluiuCurso, PDO::PARAM_INT);
            $stmt->bindValue(':aluno_id', $alunoId, PDO::PARAM_INT);
            $stmt->bindValue(':oferta_id', $ofertaId, PDO::PARAM_INT);
    
            $stmt->execute();
        }
    
        // Redireciona de volta para a página de notas ou para o dashboard
        header('Location: index.php?controller=Professor&action=notas&oferta_id=' . $ofertaId . '&success=1');
        exit;
    }

    private function checkAccess($ofertaId) {
        if (!is_professor() || !$ofertaId) return false;

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM professor WHERE oferta_id = :oferta_id AND usuarios_id = :professor_id");
        $stmt->execute([':oferta_id' => $ofertaId, ':professor_id' => $_SESSION['user_id']]);
        return $stmt->fetchColumn() > 0;
    }
}