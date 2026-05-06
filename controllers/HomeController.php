<?php

class HomeController {
    /**
     * Exibe a página inicial (landing page).
     */
    public function index() {
        require_once __DIR__ . '/../models/Oferta.php';
        $ofertas = Oferta::findAll();
        require_once __DIR__ . '/../views/home/index.php';
    }

    public function validarCertificado() {
        header('Content-Type: application/json');
        
        $codigo = $_POST['codigo'] ?? '';
        
        if (empty($codigo)) {
            echo json_encode(['success' => false, 'message' => 'Código não informado.']);
            exit;
        }

        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance()->getConnection();

        $query = "SELECT a.nome as aluno_nome, o.nome as curso_nome, o.inicio_aulas, o.fim_aulas 
                  FROM alunos_has_oferta aho
                  JOIN alunos a ON aho.alunos_id = a.id
                  JOIN oferta o ON aho.oferta_id = o.id
                  WHERE aho.codigo_validacao = :codigo AND aho.concluiu_curso = 1";
                  
        $stmt = $db->prepare($query);
        $stmt->execute([':codigo' => $codigo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $inicio = !empty($resultado['inicio_aulas']) ? date('d/m/Y', strtotime($resultado['inicio_aulas'])) : 'Não definido';
            $fim = !empty($resultado['fim_aulas']) ? date('d/m/Y', strtotime($resultado['fim_aulas'])) : 'Não definido';
            
            $mensagem = "O certificado é válido e pertence ao aluno(a) <strong>" . htmlspecialchars($resultado['aluno_nome']) . "</strong> que cursou <strong>" . htmlspecialchars($resultado['curso_nome']) . "</strong> no período de " . $inicio . " a " . $fim . ".";
            echo json_encode(['success' => true, 'message' => $mensagem]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Código de certificado inválido ou não encontrado.']);
        }
        exit;
    }
}
