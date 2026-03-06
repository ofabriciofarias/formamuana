<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../core/helpers.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma Muana - Inscrição</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="container nav-flex">
        <div class="logo">
            <a href="index.php?action=aluno_dashboard"><i class="fas fa-graduation-cap"></i> Forma Muana</a>
        </div>
        
        <nav class="nav-links" id="nav-links">
            <a href="index.php?action=aluno_dashboard">Início</a>
            <a href="index.php?action=aluno_dashboard#ofertas">Vagas</a>
            <a href="index.php?action=aluno_dashboard#cursos">Cursos</a>

            <a href="index.php?action=aluno_profile" class="btn btn-primary" style="margin-left: 15px; padding: 8px 20px;">Meu Perfil</a>
            <a href="index.php?action=logout" class="btn" style="background-color: #c0392b; color: white; padding: 8px 20px;">Sair</a>
        </nav>

        <div class="mobile-toggle" id="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<div class="container" style="padding-top: 120px; padding-bottom: 50px;">
    <h2 class="section-title">Formulário de Inscrição na Oferta</h2>

    <form action="index.php?action=do_inscricao_oferta&oferta_id=<?php echo htmlspecialchars($_GET['oferta_id']); ?>" method="POST">
        <fieldset>
            <legend>Informações Socioeconômicas</legend>
            <div class="form-group">
                <label for="exerce_atividade_remunerada">Exerce alguma atividade remunerada?</label>
                <select id="exerce_atividade_remunerada" name="exerce_atividade_remunerada" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Nao">Não</option>
                    <option value="As_vezes">Às vezes</option>
                </select>
            </div>
            <div class="form-group">
                <label for="vinculo_atividade">Se sim, qual o seu vínculo ou tipo de atividade?</label>
                <input type="text" id="vinculo_atividade" name="vinculo_atividade">
            </div>
            <div class="form-group">
                <label for="afinidade_atividade_curso">Sua atividade remunerada tem afinidade com a área do curso?</label>
                <select id="afinidade_atividade_curso" name="afinidade_atividade_curso">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Nao">Não</option>
                </select>
            </div>
            <div class="form-group">
                <label for="participacao_economica_familiar">Qual sua principal participação na vida econômica familiar?</label>
                <select id="participacao_economica_familiar" name="participacao_economica_familiar">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                    <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                    <option value="Nao_contribuo">Não contribuo com as despesas</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_membros_familia">Quantas pessoas, incluindo você, moram em sua residência?</label>
                <select id="numero_membros_familia" name="numero_membros_familia">
                    <option value="" disabled selected>Selecione</option>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="renda_familiar">Qual a renda total da sua família (incluindo você)?</label>
                <select id="renda_familiar" name="renda_familiar">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Ate_1_sm">Até 1 salário mínimo</option>
                    <option value="Entre_1_e_2_sm">Entre 1 e 2 salários mínimos</option>
                    <option value="Entre_2_e_3_sm">Entre 2 e 3 salários mínimos</option>
                    <option value="Entre_3_e_5_sm">Entre 3 e 5 salários mínimos</option>
                    <option value="Acima_5_sm">Acima de 5 salários mínimos</option>
                    <option value="Nao_sei">Não sei informar</option>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend>Sobre o Curso</legend>
            <div class="form-group">
                <label for="motivo_escolha_curso">Por que você escolheu este curso? Quais habilidades espera desenvolver?</label>
                <textarea id="motivo_escolha_curso" name="motivo_escolha_curso" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="o_que_mais_espera_do_curso">O que você mais espera do curso?</label>
                <textarea id="o_que_mais_espera_do_curso" name="o_que_mais_espera_do_curso" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="expectativas_relacao_curso">Quais suas expectativas em relação ao curso?</label>
                <textarea id="expectativas_relacao_curso" name="expectativas_relacao_curso" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="expectativas_profissionais_pos_curso">Quais suas expectativas profissionais após a conclusão do curso?</label>
                <textarea id="expectativas_profissionais_pos_curso" name="expectativas_profissionais_pos_curso" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="canal_informacao_curso">Como você ficou sabendo deste curso?</label>
                <select id="canal_informacao_curso" name="canal_informacao_curso">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Redes_sociais">Redes Sociais (Instagram, Facebook, etc.)</option>
                    <option value="Amigos_familiares">Indicação de amigos ou familiares</option>
                    <option value="Site_instituicao">Site da Instituição</option>
                    <option value="Busca_internet">Busca na Internet (Google, etc.)</option>
                    <option value="Radio_tv">Rádio ou TV</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="turno_preferencia">Qual o seu turno de preferência para estudar?</label>
                <select id="turno_preferencia" name="turno_preferencia">
                    <option value="" disabled selected>Selecione</option>
                    <option value="Manha">Manhã</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Noite">Noite</option>
                    <option value="Indiferente">Indiferente</option>
                </select>
            </div>
        </fieldset>
        
        <fieldset>
            <legend>Termo de Aceite</legend>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="termo_aceite" name="termo_aceite" value="Sim" required style="width: auto;">
                <label for="termo_aceite" style="margin-bottom: 0;">Você concorda com os termos desse projeto?</label>
            </div>
        </fieldset>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;">Finalizar Inscrição</button>
    </form>
</div>

<footer>
    <div class="container">
        <p>© <?= date('Y') ?> Forma Muana. Feito com <i class="fas fa-heart" style="color: #fab1a0;"></i> para o seu futuro.</p>
    </div>
</footer>

<script>
    const mobileToggle = document.getElementById('mobile-toggle');
    const navLinks = document.getElementById('nav-links');

    mobileToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        const icon = mobileToggle.querySelector('i');
        if(navLinks.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
</script>
</body>
</html>
