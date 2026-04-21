<?php

class OfertaController
{
    public function create()
    {
        $users = Oferta::getAllUsers();
        require_once 'views/oferta/register.php';
    }

    public function store()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $required_fields = [
            'nome'             => 'Nome',
            'descricao'        => 'Descrição',
            'eixo_tecnologico' => 'Eixo Tecnológico',
            'inicio_insc'      => 'Início das Inscrições',
            'final_insc'       => 'Final das Inscrições',
            'status_oferta'    => 'Status da Oferta',
            'usuarios_id'      => 'Usuário Responsável',
            'local'            => 'Local',
            'turno'            => 'Turno',
            'numero_vagas'     => 'Número de Vagas',
            'publico_alvo'     => 'Público Alvo',
            'modalidade'       => 'Modalidade',
            'carga_horaria'    => 'Carga Horária',
        ];

        $errors = [];
        foreach ($required_fields as $field => $label) {
            if (empty(trim($_POST[$field] ?? ''))) {
                $errors[] = "O campo '{$label}' é obrigatório.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors']   = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: /admin/oferta/nova');
            exit;
        }

        try {
            $foto_path      = null;
            $relatorio_path = null;

            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $foto_path = 'uploads/' . uniqid() . '-' . basename($_FILES['foto']['name']);
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
                    throw new Exception('Erro ao mover o arquivo de foto.');
                }
            }

            if (isset($_FILES['upload_relatorio']) && $_FILES['upload_relatorio']['error'] == UPLOAD_ERR_OK) {
                $relatorio_path = 'uploads/' . uniqid() . '-' . basename($_FILES['upload_relatorio']['name']);
                if (!move_uploaded_file($_FILES['upload_relatorio']['tmp_name'], $relatorio_path)) {
                    throw new Exception('Erro ao mover o arquivo de relatório.');
                }
            }

            Oferta::create([
                'nome'             => htmlspecialchars($_POST['nome']),
                'descricao'        => htmlspecialchars($_POST['descricao']),
                'eixo_tecnologico' => htmlspecialchars($_POST['eixo_tecnologico']),
                'inicio_insc'      => htmlspecialchars($_POST['inicio_insc']),
                'final_insc'       => htmlspecialchars($_POST['final_insc']),
                'status_oferta'    => htmlspecialchars($_POST['status_oferta']),
                'usuarios_id'      => htmlspecialchars($_POST['usuarios_id']),
                'local'            => htmlspecialchars($_POST['local']),
                'turno'            => htmlspecialchars($_POST['turno']),
                'numero_vagas'     => htmlspecialchars($_POST['numero_vagas']),
                'publico_alvo'     => htmlspecialchars($_POST['publico_alvo']),
                'modalidade'       => htmlspecialchars($_POST['modalidade']),
                'carga_horaria'    => htmlspecialchars($_POST['carga_horaria']),
                'foto'             => $foto_path,
                'upload_relatorio' => $relatorio_path,
                'inicio_aulas'     => !empty($_POST['inicio_aulas'])     ? htmlspecialchars($_POST['inicio_aulas'])     : null,
                'fim_aulas'        => !empty($_POST['fim_aulas'])        ? htmlspecialchars($_POST['fim_aulas'])        : null,
                'inicio_hora_aula' => !empty($_POST['inicio_hora_aula']) ? htmlspecialchars($_POST['inicio_hora_aula']) : null,
                'fim_hora_aula'    => !empty($_POST['fim_hora_aula'])    ? htmlspecialchars($_POST['fim_hora_aula'])    : null,
            ]);

            $_SESSION['success_message'] = 'Oferta cadastrada com sucesso!';
            header('Location: /admin');
            exit;
        } catch (Exception $e) {
            $_SESSION['errors']   = ['Erro: ' . $e->getMessage()];
            $_SESSION['old_data'] = $_POST;
            header('Location: /admin/oferta/nova');
            exit;
        }
    }
}
