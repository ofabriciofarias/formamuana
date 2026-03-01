<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/helpers.php';

class AlunoController {

    /**
     * Exibe o formulário de cadastro de aluno dentro do layout do admin.
     */
    public function showAlunoRegistrationForm() {
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }

        require_once __DIR__ . '/../views/layouts/admin_header.php';
        require_once __DIR__ . '/../views/aluno/register.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    /**
     * Processa o envio do formulário de cadastro de aluno.
     */
    public function registerAluno() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_admin()) {
            header('Location: index.php');
            exit;
        }

        // Mapeia todos os campos do formulário para variáveis
        $fields = [
            'nome', 'whatsapp', 'telefone', 'email', 'data_nascimento', 'sexo', 
            'identidade_genero', 'possui_nome_social', 'nome_social', 'possui_deficiencia', 
            'tipo_deficiencia', 'cor_raca', 'comunidade_tradicional', 'zona_residencia', 
            'cidade_origem', 'cidade_atual', 'bairro_comunidade', 'estado_civil', 
            'possui_filhos', 'quantidade_filhos', 'tipo_escola_ensino_medio', 
            'nivel_escolaridade', 'beneficiario_programa_social', 'acesso_internet', 
            'dispositivo_principal'
        ];

        $alunoData = [];
        foreach ($fields as $field) {
            $alunoData[$field] = $_POST[$field] ?? null;
        }

        // Validação básica (apenas para o nome, por exemplo)
        if (empty($alunoData['nome'])) {
            die('O campo "Nome Completo" é obrigatório.');
        }

        // Prepara a query de inserção
        $columns = implode(', ', array_keys($alunoData));
        $placeholders = ':' . implode(', :', array_keys($alunoData));
        $sql = "INSERT INTO alunos ($columns) VALUES ($placeholders)";

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare($sql);
            
            // Binda todos os parâmetros
            foreach ($alunoData as $key => &$value) {
                $stmt->bindParam(':' . $key, $value);
            }

            $stmt->execute();

            // Exibe mensagem de sucesso com redirecionamento
            require_once __DIR__ . '/../views/layouts/admin_header.php';
            echo "<div>
                    <h2>Aluno cadastrado com sucesso!</h2>
                    <p>Redirecionando de volta para a página de administração...</p>
                  </div>
                  <script>
                      setTimeout(function() {
                          window.location.href = 'index.php?action=register';
                      }, 3000); // Redireciona após 3 segundos
                  </script>";
            require_once __DIR__ . '/../views/layouts/admin_footer.php';

        } catch (PDOException $e) {
            die("Erro ao cadastrar aluno: " . $e->getMessage());
        }
    }
}
