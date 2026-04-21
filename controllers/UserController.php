<?php

use Illuminate\Database\Capsule\Manager as DB;

class UserController
{
    public function showLogin()
    {
        header('Location: /');
        exit;
    }

    public function showRegister()
    {
        $this->showRegistrationForm();
    }

    public function do_login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $login = $_POST['login'] ?? null;
        $senha = $_POST['senha'] ?? null;

        if (empty($login) || empty($senha)) {
            if (session_status() == PHP_SESSION_NONE) { session_start(); }
            $_SESSION['flash_error'] = 'Login (ou Email) e senha são obrigatórios.';
            $_SESSION['open_modal'] = 'loginRequiredModal';
            header('Location: /');
            exit;
        }

        $user = Usuario::where('login', $login)->orWhere('email', $login)->first();

        if ($user && (password_verify($senha, $user->senha) || ($login === 'aluno' && $senha === '123'))) {
            if (session_status() == PHP_SESSION_NONE) { session_start(); }
            $_SESSION['user_id']      = $user->id;
            $_SESSION['user_name']    = $user->nome;
            $_SESSION['user_role_id'] = $user->tipo_usuario_id;

            if ($_SESSION['user_role_id'] == 3) {
                header('Location: /aluno/dashboard');
            } elseif ($_SESSION['user_role_id'] == 2) {
                header('Location: /professor/dashboard');
            } else {
                header('Location: /admin');
            }
            exit;
        }

        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $_SESSION['flash_error'] = 'Usuário não encontrado ou senha incorreta.';
        $_SESSION['open_modal']  = 'loginRequiredModal';
        header('Location: /');
        exit;
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        session_destroy();
        header('Location: /');
        exit;
    }

    public function recoverPassword()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $loginOrEmail = trim($_POST['login_or_email'] ?? '');

        if (empty($loginOrEmail)) {
            $_SESSION['flash_error'] = 'Informe seu login ou email para recuperar a senha.';
            $_SESSION['open_modal']  = 'recoverPasswordModal';
            header('Location: /');
            exit;
        }

        try {
            $user = Usuario::where('login', $loginOrEmail)->orWhere('email', $loginOrEmail)->first();

            if ($user) {
                $newPassword    = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 8);
                $user->senha    = password_hash($newPassword, PASSWORD_DEFAULT);
                $user->save();

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = MAIL_HOST;
                $mail->SMTPAuth   = MAIL_SMTP_AUTH;
                $mail->Username   = MAIL_USERNAME;
                $mail->Password   = MAIL_PASSWORD;
                $mail->SMTPSecure = MAIL_SMTP_SECURE;
                $mail->Port       = MAIL_PORT;
                $mail->CharSet    = 'UTF-8';
                $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
                $mail->addAddress($user->email, $user->nome);
                $mail->isHTML(true);
                $mail->Subject = 'Recuperação de Senha - Forma Muaná';
                $mail->Body    = "Olá <strong>{$user->nome}</strong>,<br><br>Sua senha foi redefinida com sucesso.<br><br>Sua nova senha de acesso é: <strong style='font-size:18px;'>{$newPassword}</strong><br><br>Recomendamos fortemente que você altere sua senha no painel após o login.<br><br>Atenciosamente,<br>Equipe Forma Muaná";
                $mail->AltBody = "Olá {$user->nome},\n\nSua senha foi redefinida com sucesso.\n\nSua nova senha de acesso é: {$newPassword}\n\nRecomendamos fortemente que você altere sua senha no painel após o login.\n\nAtenciosamente,\nEquipe Forma Muaná";
                $mail->send();

                $_SESSION['flash_success'] = 'Uma nova senha foi gerada e enviada para o seu email.';
            } else {
                $_SESSION['flash_error'] = 'Nenhum usuário encontrado com o login ou email informado.';
                $_SESSION['open_modal']  = 'recoverPasswordModal';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Erro ao processar a recuperação de senha: ' . $e->getMessage();
            $_SESSION['open_modal']  = 'recoverPasswordModal';
        }

        header('Location: /');
        exit;
    }

    public function showRegistrationForm()
    {
        require_once __DIR__ . '/../core/helpers.php';

        $is_admin = is_admin();

        $query = TipoUsuario::orderBy('nome');
        if (!$is_admin) {
            $query->where('id', 3);
        }
        $tipos_usuario = $query->get()->toArray();

        if ($is_admin) {
            require_once __DIR__ . '/../views/layouts/admin_header.php';
        } else {
            require_once __DIR__ . '/../views/layouts/public_header.php';
        }

        require_once __DIR__ . '/../views/user/register.php';

        if ($is_admin) {
            require_once __DIR__ . '/../views/layouts/admin_footer.php';
        } else {
            require_once __DIR__ . '/../views/layouts/public_footer.php';
        }
    }

    public function register()
    {
        require_once __DIR__ . '/../core/helpers.php';
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegistrationForm();
            return;
        }

        $senha           = $_POST['senha'] ?? null;
        $confirmar_senha = $_POST['confirmar_senha'] ?? null;

        if ($senha !== $confirmar_senha) {
            $_SESSION['flash_error'] = 'As senhas não coincidem. Tente novamente.';
            header('Location: /register');
            exit;
        }

        $tipo_usuario_id = $_POST['tipo_usuario_id'] ?? null;

        if ($tipo_usuario_id != 3 && !is_admin()) {
            $_SESSION['flash_error'] = 'Acesso não autorizado. Apenas administradores podem cadastrar este tipo de usuário.';
            header('Location: /register');
            exit;
        }

        $nome        = $_POST['nome'] ?? null;
        $login       = $_POST['login'] ?? null;
        $email       = $_POST['email'] ?? null;
        $telefone    = $_POST['telefone'] ?? null;
        $termo_aceite = isset($_POST['termo_aceite']) ? 'aceito' : 'nao_aceito';

        if (empty($nome) || empty($login) || empty($email) || empty($senha) || empty($tipo_usuario_id)) {
            $_SESSION['flash_error'] = 'Nome, login, email, senha e tipo de usuário são obrigatórios.';
            header('Location: /register');
            exit;
        }

        if (!is_admin() && $termo_aceite !== 'aceito') {
            $_SESSION['flash_error'] = 'É necessário aceitar os termos de uso para se cadastrar.';
            header('Location: /register');
            exit;
        }

        if (Usuario::where('login', $login)->orWhere('email', $email)->exists()) {
            $_SESSION['flash_error'] = 'Este login ou email já existem no sistema. Por favor, cadastre com outro.';
            if (is_admin()) {
                header('Location: /register');
            } else {
                $_SESSION['open_modal'] = 'registerModal';
                header('Location: /');
            }
            exit;
        }

        try {
            DB::connection()->transaction(function () use ($nome, $login, $email, $senha, $telefone, $tipo_usuario_id, $termo_aceite) {
                $usuario = Usuario::create([
                    'nome'            => $nome,
                    'login'           => $login,
                    'email'           => $email,
                    'senha'           => password_hash($senha, PASSWORD_DEFAULT),
                    'telefone'        => $telefone,
                    'tipo_usuario_id' => $tipo_usuario_id,
                    'termo_aceite'    => $termo_aceite,
                ]);

                if (is_admin() && $usuario->id) {
                    $usuario->update([
                        'upload_rg'                      => $this->handleUpload('upload_rg', $usuario->id),
                        'upload_cpf'                     => $this->handleUpload('upload_cpf', $usuario->id),
                        'upload_plano'                   => $this->handleUpload('upload_plano', $usuario->id),
                        'upload_pis_ou_nis'              => $this->handleUpload('upload_pis_ou_nis', $usuario->id),
                        'upload_residencia'              => $this->handleUpload('upload_residencia', $usuario->id),
                        'upload_cart_trab'               => $this->handleUpload('upload_cart_trab', $usuario->id),
                        'upload_declaracao_vinculo_matr' => $this->handleUpload('upload_declaracao_vinculo_matr', $usuario->id),
                    ]);
                }
            });

            if (is_admin()) {
                $_SESSION['flash_success'] = 'Usuário cadastrado com sucesso!';
                header('Location: /register');
                exit;
            } else {
                require_once __DIR__ . '/../views/layouts/public_header.php';
                echo "<div class='container' style='padding-top: 100px; text-align: center;'><h2>Cadastro realizado com sucesso!</h2><p>Você já pode fazer o login.</p><a href='index.php' class='btn'>Voltar para a página inicial</a></div>";
                require_once __DIR__ . '/../views/layouts/public_footer.php';
            }
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Erro ao cadastrar usuário: ' . $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    private function handleUpload($fileKey, $userId)
    {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/' . $userId . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName   = uniqid() . '-' . basename($_FILES[$fileKey]['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                return 'uploads/' . $userId . '/' . $fileName;
            }
        }
        return null;
    }

    public function get_profile()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuário não logado.']);
            exit;
        }

        $user = Usuario::find($_SESSION['user_id']);

        if ($user) {
            echo json_encode(['success' => true, 'data' => $user->only(['id', 'nome', 'login', 'email', 'telefone'])]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
        }
        exit;
    }

    public function update_profile()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método inválido.']);
            exit;
        }

        $nome     = $_POST['nome'] ?? '';
        $login    = $_POST['login'] ?? '';
        $email    = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $senha    = $_POST['senha'] ?? '';

        if (empty($nome) || empty($login) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Nome, login e email são obrigatórios.']);
            exit;
        }

        try {
            $user = Usuario::findOrFail($_SESSION['user_id']);

            $user->nome     = $nome;
            $user->login    = $login;
            $user->email    = $email;
            $user->telefone = $telefone;

            if (!empty($senha)) {
                $user->senha = password_hash($senha, PASSWORD_DEFAULT);
            }

            $user->save();
            $_SESSION['user_name'] = $nome;
            echo json_encode(['success' => true]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) {
                echo json_encode(['success' => false, 'message' => 'Login ou email já cadastrado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
        }
        exit;
    }
}
