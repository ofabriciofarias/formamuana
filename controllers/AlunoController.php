<?php

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../models/AlunoOferta.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Aluno.php';
require_once __DIR__ . '/../models/Oferta.php';


class AlunoController {

    /**
     * Exibe o dashboard do aluno com a lista de ofertas.
     */
    public function dashboard() {
        // Garante que apenas alunos logados acessem
        if (!is_aluno()) {
            header('Location: index.php');
            exit;
        }

        // Verifica se o aluno já completou o cadastro (tabela alunos)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM alunos WHERE usuarios_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $openCompleteProfileModal = false;
        if (!$aluno) {
            // Primeiro acesso: Redireciona para concluir o cadastro
            $openCompleteProfileModal = true;
            $aluno = []; // Garante que $aluno é um array vazio para o formulário
            $alunoId = null;
        } else {
            $alunoId = $aluno['id'];
        }

        // Busca todas as ofertas disponíveis
        $ofertas = Oferta::findAll();

        // Busca todas as inscrições do aluno
        $inscricoes = [];
        if ($alunoId) {
            $inscricoes = AlunoOferta::findAllByAluno($alunoId);
        }
        $ofertasInscritasIds = array_column($inscricoes, 'oferta_id');

        // Carrega a view do dashboard do aluno
        require_once __DIR__ . '/../views/aluno/dashboard.php';
    }


    /**
     * Exibe o formulário de cadastro de aluno dentro do layout do admin.
     */
    public function showAlunoRegistrationForm() {
        // Permite Admin ou Aluno
        if (!is_admin() && !is_aluno()) {
            header('Location: index.php');
            exit;
        }

        $aluno = []; // Dados vazios por padrão
        $formAction = 'index.php?action=register_aluno';
        $isEditing = false;

        // Se for aluno, define o header público/aluno. Se admin, header admin.
        if (is_admin()) {
            require_once __DIR__ . '/../views/layouts/admin_header.php';
        } else {
            require_once __DIR__ . '/../views/layouts/header.php';
            echo '<div class="container" style="padding-top: 120px;">';
            if (isset($_GET['msg']) && $_GET['msg'] == 'complete_cadastro') {
                echo '<div class="alert" style="background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <i class="fas fa-info-circle"></i> Por favor, complete seu cadastro para acessar o painel.
                      </div>';
            }
        }

        require_once __DIR__ . '/../views/aluno/register.php';

        if (is_admin()) {
            require_once __DIR__ . '/../views/layouts/admin_footer.php';
        } else {
            echo '</div>'; // Fecha container
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }

    /**
     * Processa o envio do formulário de cadastro de aluno.
     */
    public function registerAluno() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }
        
        // Permite Admin ou Aluno
        if (!is_admin() && !is_aluno()) {
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

        // Se for Aluno logado, vincula o ID do usuário automaticamente
        if (is_aluno()) {
            $alunoData['usuarios_id'] = $_SESSION['user_id'];
        }

        // Validação básica (apenas para o nome, por exemplo)
        if (empty($alunoData['nome'])) {
            die('O campo "Nome Completo" é obrigatório.');
        }

        // Prepara a query de inserção
        $keys = array_keys($alunoData);
        $columns = implode(', ', $keys);
        $placeholders = ':' . implode(', :', $keys);
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
            if (is_admin()) {
                require_once __DIR__ . '/../views/layouts/admin_header.php';
                $redirectUrl = 'index.php?action=register';
            } else {
                require_once __DIR__ . '/../views/layouts/header.php';
                $redirectUrl = 'index.php?action=aluno_dashboard';
            }

            echo "<div>
                    <div class='container' style='padding-top: 120px; text-align: center;'>
                        <h2>Dados salvos com sucesso!</h2>
                        <p>Redirecionando...</p>
                    </div>
                  </div>
                  <script>
                      setTimeout(function() {
                          window.location.href = '$redirectUrl';
                      }, 2000); 
                  </script>";
            // Footer é opcional aqui pois vai redirecionar, mas mantemos a boa prática se necessário

        } catch (PDOException $e) {
            die("Erro ao cadastrar aluno: " . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário de edição de perfil (mesma view do registro, mas populada).
     */
    public function profile() {
        if (!is_aluno()) {
            header('Location: index.php');
            exit;
        }

        $db = Database::getInstance()->getConnection();
        // Busca dados do aluno pelo ID do usuário logado
        $stmt = $db->prepare("SELECT * FROM alunos WHERE usuarios_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aluno) {
            // Se não existir, manda para o cadastro
            header('Location: index.php?action=show_register_aluno_form');
            exit;
        }

        $formAction = 'index.php?action=update_aluno';
        $isEditing = true;

        require_once __DIR__ . '/../views/layouts/header.php';
        echo '<div class="container" style="padding-top: 120px;">
                <div class="section-title"><h2>Meu Perfil</h2></div>';
        require_once __DIR__ . '/../views/aluno/register.php';
        echo '</div>';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Processa a atualização dos dados do aluno.
     */
    public function updateAluno() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_aluno()) {
            header('Location: index.php');
            exit;
        }

        $fields = [
            'nome', 'whatsapp', 'telefone', 'email', 'data_nascimento', 'sexo', 
            'identidade_genero', 'possui_nome_social', 'nome_social', 'possui_deficiencia', 
            'tipo_deficiencia', 'cor_raca', 'comunidade_tradicional', 'zona_residencia', 
            'cidade_origem', 'cidade_atual', 'bairro_comunidade', 'estado_civil', 
            'possui_filhos', 'quantidade_filhos', 'tipo_escola_ensino_medio', 
            'nivel_escolaridade', 'beneficiario_programa_social', 'acesso_internet', 
            'dispositivo_principal'
        ];

        $updateParts = [];
        $params = [];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $updateParts[] = "$field = :$field";
                $params[":$field"] = $_POST[$field];
            }
        }

        // Adiciona o ID do usuário para o WHERE
        $params[':uid'] = $_SESSION['user_id'];

        if (empty($updateParts)) {
            header('Location: index.php?action=aluno_profile');
            exit;
        }

        $sql = "UPDATE alunos SET " . implode(', ', $updateParts) . " WHERE usuarios_id = :uid";

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            header('Location: index.php?action=aluno_dashboard');
            exit;
        } catch (PDOException $e) {
            die("Erro ao atualizar perfil: " . $e->getMessage());
        }
    }

    /**
     * Exibe o formulário de inscrição em uma oferta.
     */
    public function showInscricaoForm() {
        if (!is_aluno()) {
            header('Location: index.php');
            exit;
        }

        if (!isset($_GET['oferta_id'])) {
            die('Erro: ID da oferta não especificado.');
        }

        require_once __DIR__ . '/../views/aluno/inscrever.php';
    }

    /**
     * Processa a submissão do formulário de inscrição em uma oferta.
     */
    public function doInscricaoOferta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php');
            exit;
        }

        if (!is_aluno()) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!isset($_GET['oferta_id'])) {
            die('Erro: ID da oferta ausente.');
        }

        if (!isset($_SESSION['user_id'])) {
            die('Erro: Sessão de usuário inválida. Faça o login novamente.');
        }

        $alunoId = null;
        if (isset($_SESSION['aluno_id'])) {
            // Caso 1: Aluno logado diretamente pela tabela de alunos (fallback)
            $alunoId = $_SESSION['aluno_id'];
        } elseif (isset($_SESSION['user_id'])) {
            // Caso 2: Aluno logado pela tabela de usuários (padrão)
            $usuario = Usuario::findById($_SESSION['user_id']);
            if ($usuario) {
                $aluno = Aluno::findByEmail($usuario->getEmail());
                if ($aluno) {
                    $alunoId = $aluno->getId();
                }
            }
        }

        if (!$alunoId) {
            die('Erro: Não foi possível identificar os dados do aluno para a inscrição. Verifique se seu cadastro está completo.');
        }

        // Verifica se já existe uma inscrição
        $ofertaId = $_GET['oferta_id'];
        $existingInscricao = AlunoOferta::findByAlunoAndOferta($alunoId, $ofertaId);
        if ($existingInscricao) {
            echo "<script>alert('Sua inscrição para esta vaga já foi realizada.'); window.location.href = 'index.php?action=aluno_dashboard';</script>";
            exit;
        }

        // Etapa 3: Prosseguir com a criação da inscrição
        $inscricao = new AlunoOferta();
        $inscricao->setAlunosId($alunoId); 
        $inscricao->setOfertaId($ofertaId);
        $inscricao->setStatusSelecao('pendente');

        // Mapeia o restante dos campos do formulário
        $inscricao->setExerceAtividadeRemunerada($_POST['exerce_atividade_remunerada'] ?? null);
        $inscricao->setVinculoAtividade($_POST['vinculo_atividade'] ?? null);
        $inscricao->setAfinidadeAtividadeCurso($_POST['afinidade_atividade_curso'] ?? null);
        $inscricao->setParticipacaoEconomicaFamiliar($_POST['participacao_economica_familiar'] ?? null);
        $inscricao->setNumeroMembrosFamilia($_POST['numero_membros_familia'] ?? null);
        $inscricao->setRendaFamiliar($_POST['renda_familiar'] ?? null);
        $inscricao->setMotivoEscolhaCurso($_POST['motivo_escolha_curso'] ?? null);
        $inscricao->setOQueMaisEsperaDoCurso($_POST['o_que_mais_espera_do_curso'] ?? null);
        $inscricao->setExpectativasRelacaoCurso($_POST['expectativas_relacao_curso'] ?? null);
        $inscricao->setExpectativasProfissionaisPosCurso($_POST['expectativas_profissionais_pos_curso'] ?? null);
        $inscricao->setCanalInformacaoCurso($_POST['canal_informacao_curso'] ?? null);
        $inscricao->setTurnoPreferencia($_POST['turno_preferencia'] ?? null);
        
        $termo = isset($_POST['termo_aceite']) ? 'Concordo com os termos do projeto.' : 'Não concordou.';
        $inscricao->setTermoAceite($termo);

        if ($inscricao->save()) {
            echo "<script>alert('Inscrição realizada com sucesso!'); window.location.href = 'index.php?action=aluno_dashboard';</script>";
        } else {
            // Caso retorne false (ex: já inscrito)
            echo "<div class='container' style='padding-top: 100px; text-align: center; font-family: sans-serif;'>
                    <h2 style='color: #c0392b;'>Atenção</h2>
                    <p>Não foi possível realizar a inscrição. Você já pode estar inscrito nesta oferta ou ocorreu um erro.</p>
                    <a href='index.php?action=aluno_dashboard' style='text-decoration: underline;'>Voltar ao Painel</a>
                  </div>
                  ";
        }
    }
}
