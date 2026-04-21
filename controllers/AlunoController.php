<?php

class AlunoController
{
    public function dashboard()
    {
        if (!is_aluno()) {
            header('Location: /');
            exit;
        }

        $aluno = Aluno::where('usuarios_id', $_SESSION['user_id'])->first();

        $openCompleteProfileModal = false;
        $alunoId = null;

        if (!$aluno) {
            $openCompleteProfileModal = true;

            $user  = Usuario::find($_SESSION['user_id']);
            $aluno = $user
                ? ['nome' => $user->nome, 'email' => $user->email, 'whatsapp' => $user->telefone]
                : [];
        } else {
            $alunoId = $aluno->id;
        }

        $ofertas = Oferta::findAll();

        $inscricoes      = [];
        $minhasInscricoes = [];
        if ($alunoId) {
            $inscricoes       = AlunoOferta::findAllByAluno($alunoId);
            $minhasInscricoes = AlunoOferta::findInscricoesByAlunoId($alunoId);
        }
        $ofertasInscritasIds = array_column($inscricoes, 'oferta_id');

        require_once __DIR__ . '/../views/layouts/aluno_header.php';
        require_once __DIR__ . '/../views/aluno/dashboard.php';
        require_once __DIR__ . '/../views/layouts/footer.php';
    }

    public function showAlunoRegistrationForm()
    {
        if (!is_admin() && !is_aluno()) {
            header('Location: /');
            exit;
        }

        $aluno      = [];
        $formAction = '/aluno/cadastro';
        $isEditing  = false;

        if (is_admin()) {
            require_once __DIR__ . '/../views/layouts/admin_header.php';
        } else {
            require_once __DIR__ . '/../views/layouts/aluno_header.php';
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
            echo '</div>';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }

    public function registerAluno()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        if (!is_admin() && !is_aluno()) {
            header('Location: /');
            exit;
        }

        if (isset($_POST['possui_filhos']) && $_POST['possui_filhos'] === 'Nao') {
            $_POST['quantidade_filhos'] = 0;
        }

        $fields = [
            'nome', 'whatsapp', 'telefone', 'email', 'data_nascimento', 'sexo',
            'identidade_genero', 'possui_nome_social', 'nome_social', 'possui_deficiencia',
            'tipo_deficiencia', 'cor_raca', 'comunidade_tradicional', 'zona_residencia',
            'cidade_origem', 'cidade_atual', 'bairro_comunidade', 'estado_civil',
            'possui_filhos', 'quantidade_filhos', 'tipo_escola_ensino_medio',
            'nivel_escolaridade', 'beneficiario_programa_social', 'acesso_internet',
            'dispositivo_principal',
        ];

        $alunoData = [];
        foreach ($fields as $field) {
            $alunoData[$field] = $_POST[$field] ?? null;
        }

        if (is_aluno()) {
            $alunoData['usuarios_id'] = $_SESSION['user_id'];
        }

        if (empty($alunoData['nome'])) {
            die('O campo "Nome Completo" é obrigatório.');
        }

        try {
            Aluno::create($alunoData);

            $redirectUrl = is_admin() ? '/register' : '/aluno/dashboard';

            if (is_admin()) {
                require_once __DIR__ . '/../views/layouts/admin_header.php';
            } else {
                require_once __DIR__ . '/../views/layouts/aluno_header.php';
            }

            echo "<div>
                    <div class='container' style='padding-top: 120px; text-align: center;'>
                        <h2>Dados salvos com sucesso!</h2>
                        <p>Redirecionando...</p>
                    </div>
                  </div>
                  <script>
                      setTimeout(function() {
                          window.location.href = '{$redirectUrl}';
                      }, 2000);
                  </script>";

            if (!is_admin()) {
                require_once __DIR__ . '/../views/layouts/footer.php';
            }
        } catch (Exception $e) {
            die('Erro ao cadastrar aluno: ' . $e->getMessage());
        }
    }

    public function updateAluno()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_aluno()) {
            header('Location: /');
            exit;
        }

        if (isset($_POST['possui_filhos']) && $_POST['possui_filhos'] === 'Nao') {
            $_POST['quantidade_filhos'] = 0;
        }

        $fields = [
            'nome', 'whatsapp', 'telefone', 'email', 'data_nascimento', 'sexo',
            'identidade_genero', 'possui_nome_social', 'nome_social', 'possui_deficiencia',
            'tipo_deficiencia', 'cor_raca', 'comunidade_tradicional', 'zona_residencia',
            'cidade_origem', 'cidade_atual', 'bairro_comunidade', 'estado_civil',
            'possui_filhos', 'quantidade_filhos', 'tipo_escola_ensino_medio',
            'nivel_escolaridade', 'beneficiario_programa_social', 'acesso_internet',
            'dispositivo_principal',
        ];

        $updateData = [];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $updateData[$field] = $_POST[$field];
            }
        }

        if (empty($updateData)) {
            header('Location: /perfil');
            exit;
        }

        try {
            Aluno::where('usuarios_id', $_SESSION['user_id'])->update($updateData);
            header('Location: /aluno/dashboard');
            exit;
        } catch (Exception $e) {
            die('Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    public function showInscricaoForm()
    {
        if (!is_aluno()) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Acesso negado. Por favor, faça o login como aluno.';
            exit;
        }

        if (!isset($_GET['oferta_id'])) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Erro: ID da oferta não especificado.';
            exit;
        }

        $oferta = Oferta::findById($_GET['oferta_id']);
        if (!$oferta) {
            header('HTTP/1.1 404 Not Found');
            echo 'Oferta não encontrada.';
            exit;
        }

        require_once __DIR__ . '/../views/aluno/inscrever.php';
    }

    public function doInscricaoOferta()
    {
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        if (!is_aluno()) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Acesso negado. Faça o login novamente.']);
                exit;
            }
            header('Location: /login');
            exit;
        }

        if (!isset($_GET['oferta_id'])) {
            $message = 'Erro: ID da oferta ausente.';
            if ($isAjax) { http_response_code(400); header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => $message]); exit; }
            die($message);
        }

        if (!isset($_SESSION['user_id'])) {
            $message = 'Erro: Sessão de usuário inválida. Faça o login novamente.';
            if ($isAjax) { http_response_code(401); header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => $message]); exit; }
            die($message);
        }

        $alunoId = null;
        if (isset($_SESSION['aluno_id'])) {
            $alunoId = $_SESSION['aluno_id'];
        } else {
            $usuario = Usuario::find($_SESSION['user_id']);
            if ($usuario) {
                $aluno = Aluno::findByEmail($usuario->email);
                if ($aluno) {
                    $alunoId = $aluno->id;
                }
            }
        }

        if (!$alunoId) {
            $message = 'Erro: Não foi possível identificar os dados do aluno para a inscrição. Verifique se seu cadastro está completo.';
            if ($isAjax) { http_response_code(400); header('Content-Type: application/json'); echo json_encode(['success' => false, 'message' => $message]); exit; }
            die($message);
        }

        $ofertaId = $_GET['oferta_id'];

        if (AlunoOferta::findByAlunoAndOferta($alunoId, $ofertaId)) {
            if ($isAjax) {
                http_response_code(409);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sua inscrição para esta vaga já foi realizada.']);
                exit;
            }
            echo "<script>alert('Sua inscrição para esta vaga já foi realizada.'); window.location.href = '/aluno/dashboard';</script>";
            exit;
        }

        try {
            AlunoOferta::create([
                'alunos_id'                            => $alunoId,
                'oferta_id'                            => $ofertaId,
                'status_selecao'                       => 'pendente',
                'termo_aceite'                         => isset($_POST['termo_aceite']) ? 'Concordo com os termos do projeto.' : 'Não concordou.',
                'exerce_atividade_remunerada'          => $_POST['exerce_atividade_remunerada'] ?? null,
                'vinculo_atividade'                    => $_POST['vinculo_atividade'] ?? null,
                'afinidade_atividade_curso'            => $_POST['afinidade_atividade_curso'] ?? null,
                'participacao_economica_familiar'      => $_POST['participacao_economica_familiar'] ?? null,
                'numero_membros_familia'               => $_POST['numero_membros_familia'] ?? null,
                'renda_familiar'                       => $_POST['renda_familiar'] ?? null,
                'motivo_escolha_curso'                 => $_POST['motivo_escolha_curso'] ?? null,
                'o_que_mais_espera_do_curso'           => $_POST['o_que_mais_espera_do_curso'] ?? null,
                'expectativas_relacao_curso'           => $_POST['expectativas_relacao_curso'] ?? null,
                'expectativas_profissionais_pos_curso' => $_POST['expectativas_profissionais_pos_curso'] ?? null,
                'canal_informacao_curso'               => $_POST['canal_informacao_curso'] ?? null,
                'turno_preferencia'                    => $_POST['turno_preferencia'] ?? null,
            ]);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Inscrição realizada com sucesso!']);
                exit;
            }
            echo "<script>alert('Inscrição realizada com sucesso!'); window.location.href = '/aluno/dashboard';</script>";
        } catch (Exception $e) {
            $message = 'Não foi possível realizar a inscrição. Você já pode estar inscrito nesta oferta ou ocorreu um erro.';
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $message]);
                exit;
            }
            echo "<div class='container' style='padding-top: 100px; text-align: center; font-family: sans-serif;'>
                    <h2 style='color: #c0392b;'>Atenção</h2>
                    <p>{$message}</p>
                    <a href='/aluno/dashboard' style='text-decoration: underline;'>Voltar ao Painel</a>
                  </div>";
        }
    }
}
