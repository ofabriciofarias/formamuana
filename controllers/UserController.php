<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../core/Database.php';

class UserController {

    public function showLoginForm() {
        // Este método agora é obsoleto, pois o login está na home.
        // Mantido para não quebrar o roteador se for chamado diretamente.
        header('Location: index.php');
        exit;
    }

    public function do_login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? null;
            $senha = $_POST['senha'] ?? null;

            if (empty($login) || empty($senha)) {
                header('Location: index.php?error=1');
                exit;
            }

            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT id, nome, senha, tipo_usuario_id FROM usuarios WHERE login = :login");
                $stmt->bindParam(':login', $login);
                $stmt->execute();
                
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($senha, $user['senha'])) {
                    if (session_status() == PHP_SESSION_NONE) { session_start(); }
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['nome'];
                    $_SESSION['user_role_id'] = $user['tipo_usuario_id'];

                    header('Location: index.php?action=register');
                    exit;
                } else {
                    header('Location: index.php?error=1');
                    exit;
                }

            } catch (PDOException $e) {
                die("Erro de banco de dados: " . $e->getMessage());
            }
        } else {
            header('Location: index.php');
            exit;
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        session_destroy();
        header('Location: index.php');
        exit;
    }

    public function showRegistrationForm() {
        require_once __DIR__ . '/../core/helpers.php'; // Adicionado para garantir que is_admin() funcione
        if (!is_admin()) {
            header('Location: index.php');
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, nome FROM tipo_usuario ORDER BY nome");
            $stmt->execute();
            $tipos_usuario = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $tipos_usuario = [];
            error_log("Erro ao buscar tipos de usuário: " . $e->getMessage());
        }

        require_once __DIR__ . '/../views/layouts/admin_header.php';
        require_once __DIR__ . '/../views/user/register.php';
        require_once __DIR__ . '/../views/layouts/admin_footer.php';
    }

    public function register() {
        require_once __DIR__ . '/../core/helpers.php';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegistrationForm();
            return;
        }
        
        if (!is_admin()) {
            die("Acesso não autorizado.");
        }

        $nome = $_POST['nome'] ?? null;
        $login = $_POST['login'] ?? null;
        $email = $_POST['email'] ?? null;
        $senha = $_POST['senha'] ?? null;
        $telefone = $_POST['telefone'] ?? null;
        $tipo_usuario_id = $_POST['tipo_usuario_id'] ?? null;
        $termo_aceite = isset($_POST['termo_aceite']) ? 'aceito' : 'nao_aceito';

        if (empty($nome) || empty($login) || empty($email) || empty($senha) || empty($tipo_usuario_id) || $termo_aceite !== 'aceito') {
            die('Validação falhou. Preencha todos os campos e aceite os termos.');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $stmt = $db->prepare(
                "INSERT INTO usuarios (nome, login, email, senha, telefone, tipo_usuario_id, termo_aceite) 
                 VALUES (:nome, :login, :email, :senha, :telefone, :tipo_usuario_id, :termo_aceite)"
            );

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senhaHash);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':tipo_usuario_id', $tipo_usuario_id);
            $stmt->bindParam(':termo_aceite', $termo_aceite);
            $stmt->execute();

            $userId = $db->lastInsertId();

            $upload_rg = $this->handleUpload('upload_rg', $userId);
            $upload_cpf = $this->handleUpload('upload_cpf', $userId);
            $upload_plano = $this->handleUpload('upload_plano', $userId);
            $upload_pis_ou_nis = $this->handleUpload('upload_pis_ou_nis', $userId);
            $upload_residencia = $this->handleUpload('upload_residencia', $userId);
            $upload_cart_trab = $this->handleUpload('upload_cart_trab', $userId);
            $upload_declaracao_vinculo_matr = $this->handleUpload('upload_declaracao_vinculo_matr', $userId);

            if ($userId) {
                $stmtUpdate = $db->prepare(
                    "UPDATE usuarios SET
                        upload_rg = :upload_rg, upload_cpf = :upload_cpf, upload_plano = :upload_plano,
                        upload_pis_ou_nis = :upload_pis_ou_nis, upload_residencia = :upload_residencia,
                        upload_cart_trab = :upload_cart_trab, upload_declaracao_vinculo_matr = :upload_declaracao_vinculo_matr
                    WHERE id = :id"
                );
                $stmtUpdate->execute([
                    ':upload_rg' => $upload_rg, ':upload_cpf' => $upload_cpf, ':upload_plano' => $upload_plano,
                    ':upload_pis_ou_nis' => $upload_pis_ou_nis, ':upload_residencia' => $upload_residencia,
                    ':upload_cart_trab' => $upload_cart_trab, ':upload_declaracao_vinculo_matr' => $upload_declaracao_vinculo_matr,
                    ':id' => $userId
                ]);
            }

            $db->commit();
            
            require_once __DIR__ . '/../views/layouts/admin_header.php';
            echo "<div><h2>Usuário cadastrado com sucesso!</h2><p>Todos os dados e documentos foram salvos.</p><a href='index.php?action=register' class='btn'>Cadastrar Outro Usuário</a></div>";
            require_once __DIR__ . '/../views/layouts/admin_footer.php';

        } catch (PDOException $e) {
            $db->rollBack();
            die("Erro ao cadastrar usuário: " . $e->getMessage());
        }
    }

    private function handleUpload($fileKey, $userId) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/' . $userId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '-' . basename($_FILES[$fileKey]['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                return 'uploads/' . $userId . '/' . $fileName;
            }
        }
        return null;
    }
}
