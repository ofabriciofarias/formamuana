<?php

require_once 'models/Oferta.php';

class OfertaController {
    public function create() {
        $users = Oferta::getAllUsers();
        require_once 'views/oferta/register.php';
    }

    public function store() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $errors = [];
        $required_fields = [
            'nome' => 'Nome',
            'descricao' => 'Descrição',
            'eixo_tecnologico' => 'Eixo Tecnológico',
            'inicio_insc' => 'Início das Inscrições',
            'final_insc' => 'Final das Inscrições',
            'status_oferta' => 'Status da Oferta',
            'usuarios_id' => 'Usuário Responsável',
            'local' => 'Local',
            'turno' => 'Turno',
            'numero_vagas' => 'Número de Vagas',
            'publico_alvo' => 'Público Alvo',
            'modalidade' => 'Modalidade',
            'carga_horaria' => 'Carga Horária'
        ];

        foreach ($required_fields as $field => $label) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                $errors[] = "O campo '{$label}' é obrigatório.";
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?action=create-oferta');
            exit;
        }

        $oferta = new Oferta();
        $oferta->setNome(htmlspecialchars($_POST['nome']));
        $oferta->setDescricao(htmlspecialchars($_POST['descricao']));
        $oferta->setEixoTecnologico(htmlspecialchars($_POST['eixo_tecnologico']));
        $oferta->setInicioInsc(htmlspecialchars($_POST['inicio_insc']));
        $oferta->setFinalInsc(htmlspecialchars($_POST['final_insc']));
        $oferta->setStatusOferta(htmlspecialchars($_POST['status_oferta']));
        $oferta->setUsuariosId(htmlspecialchars($_POST['usuarios_id']));
        $oferta->setLocal(htmlspecialchars($_POST['local']));
        $oferta->setTurno(htmlspecialchars($_POST['turno']));
        $oferta->setNumeroVagas(htmlspecialchars($_POST['numero_vagas']));
        $oferta->setPublicoAlvo(htmlspecialchars($_POST['publico_alvo']));
        $oferta->setModalidade(htmlspecialchars($_POST['modalidade']));
        $oferta->setCargaHoraria(htmlspecialchars($_POST['carga_horaria']));

        try {
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $foto_path = 'uploads/' . uniqid() . '-' . basename($_FILES['foto']['name']);
                if (!move_uploaded_file($_FILES['foto']['tmp_name'], $foto_path)) {
                    throw new Exception("Erro ao mover o arquivo de foto.");
                }
                $oferta->setFoto($foto_path);
            }

            if (isset($_FILES['upload_relatorio']) && $_FILES['upload_relatorio']['error'] == UPLOAD_ERR_OK) {
                $relatorio_path = 'uploads/' . uniqid() . '-' . basename($_FILES['upload_relatorio']['name']);
                 if (!move_uploaded_file($_FILES['upload_relatorio']['tmp_name'], $relatorio_path)) {
                    throw new Exception("Erro ao mover o arquivo de relatório.");
                }
                $oferta->setUploadRelatorio($relatorio_path);
            }

            if ($oferta->save()) {
                $_SESSION['success_message'] = "Oferta cadastrada com sucesso!";
                header('Location: index.php?action=dashboard');
                exit;
            } else {
                throw new Exception("Erro desconhecido ao salvar a oferta.");
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = ['Erro: ' . $e->getMessage()];
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?action=create-oferta');
            exit;
        }
    }
}
