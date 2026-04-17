<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../core/helpers.php';
require_once __DIR__ . '/../../models/Oferta.php';

$oferta_id = $_GET['oferta_id'] ?? null;
$turno_oferta = '';

if ($oferta_id) {
    $oferta = Oferta::findById($oferta_id);
    if ($oferta) {
        $turno_oferta = $oferta->getTurno();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma Muana - Inscrição</title>
    <link rel="stylesheet" href="/views/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="container nav-flex">
        <div class="logo">
            <a href="/aluno/dashboard"><i class="fas fa-graduation-cap"></i> Forma Muana</a>
        </div>
        
        <nav class="nav-links" id="nav-links">
            <a href="/aluno/dashboard">Início</a>
            <a href="/aluno/dashboard#ofertas">Vagas</a>
            <a href="/aluno/dashboard#cursos">Cursos</a>

            <a href="/perfil" class="btn btn-primary" style="margin-left: 15px; padding: 8px 20px;">Meu Perfil</a>
            <a href="/logout" class="btn" style="background-color: #c0392b; color: white; padding: 8px 20px;">Sair</a>
        </nav>

        <div class="mobile-toggle" id="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<div class="container" style="padding-top: 120px; padding-bottom: 50px;">
    <h2 class="section-title">Formulário de Inscrição na Oferta</h2>

    <form action="/aluno/inscricao?oferta_id=<?php echo htmlspecialchars($_GET['oferta_id']); ?>" method="POST">
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
                <input type="text" id="vinculo_atividade" name="vinculo_atividade" required>
            </div>
            <div class="form-group">
                <label for="afinidade_atividade_curso">Sua atividade remunerada tem afinidade com a área do curso?</label>
                <select id="afinidade_atividade_curso" name="afinidade_atividade_curso" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Nao">Não</option>
                </select>
            </div>
            <div class="form-group">
                <label for="participacao_economica_familiar">Qual sua principal participação na vida econômica familiar?</label>
                <select id="participacao_economica_familiar" name="participacao_economica_familiar" required>
                    <option value="" disabled selected>Selecione</option>
                    <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                    <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                    <option value="Nao_contribuo">Não contribuo com as despesas</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="numero_membros_familia">Quantas pessoas, incluindo você, moram em sua residência?</label>
                <select id="numero_membros_familia" name="numero_membros_familia" required>
                    <option value="" disabled selected>Selecione</option>
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="renda_familiar">Qual a renda total da sua família (incluindo você)?</label>
                <select id="renda_familiar" name="renda_familiar" required>
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
                <textarea id="motivo_escolha_curso" name="motivo_escolha_curso" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="o_que_mais_espera_do_curso">O que você mais espera do curso?</label>
                <textarea id="o_que_mais_espera_do_curso" name="o_que_mais_espera_do_curso" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="expectativas_relacao_curso">Quais suas expectativas em relação ao curso?</label>
                <textarea id="expectativas_relacao_curso" name="expectativas_relacao_curso" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="expectativas_profissionais_pos_curso">Quais suas expectativas profissionais após a conclusão do curso?</label>
                <textarea id="expectativas_profissionais_pos_curso" name="expectativas_profissionais_pos_curso" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="canal_informacao_curso">Como você ficou sabendo deste curso?</label>
                <select id="canal_informacao_curso" name="canal_informacao_curso" required>
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
                <input type="text" id="turno_preferencia" name="turno_preferencia" value="<?= htmlspecialchars($turno_oferta) ?>" class="form-control" readonly>
            </div>
        </fieldset>
        
        <fieldset>
            <legend>Termo de Aceite</legend>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="termo_aceite" name="termo_aceite" value="Sim" required style="width: auto;"/>
                <label for="termo_aceite" style="margin-bottom: 0;">Li e concordo com os termos.</label>
                <a href="#" onclick="event.preventDefault(); openModal('inscricaoTermsModalStandalone');" class="terms-link">(Saber mais)</a>
            </div>
        </fieldset>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;">Finalizar Inscrição</button>
    </form>
</div>

<!-- Modal de Termos de Inscrição -->
<div id="inscricaoTermsModalStandalone" class="modal modal-on-top">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('inscricaoTermsModalStandalone')">&times;</span>
        <div class="modal-header">
            <h2>Termo de Aceite e Compromisso de Participação</h2>
        </div>
        <div class="modal-body modal-body-scrollable">
            <p>Ao prosseguir com esta inscrição, você declara estar ciente e concordar com as normas e diretrizes do projeto de extensão "Ilha do Marajó em Foco: Oferta de Cursos de Capacitação e Formação Técnica na Cidade de Muaná".</p>
            <p>Este projeto é uma política pública realizada pela Universidade Federal do Pará (UFPA), com financiamento da Secretaria de Ciência, Tecnologia e Educação Profissional e Tecnológica do Estado do Pará (SECTET).</p>

            <h4>1. Frequência e Desempenho</h4>
            <ul>
                <li><strong>Assiduidade:</strong> Comprometo-me a participar das aulas, estando ciente de que a obtenção do certificado está condicionada à frequência mínima de 75% da carga horária total.</li>
                <li><strong>Comprometimento:</strong> Declaro que me empenharei na realização das atividades propostas, buscando o melhor aproveitamento técnico e pedagógico do curso.</li>
                <li><strong>Conduta:</strong> Manterei uma postura de respeito e urbanidade com tutores, professores, coordenadores e demais colegas de turma.</li>
            </ul>

            <h4>2. Certificação e Entrega</h4>
            <ul>
                <li><strong>Cerimônia de Entrega:</strong> Responsabilizo-me a comparecer à cerimônia de certificação, momento oficial e único para a entrega física dos certificados.</li>
                <li><strong>Exceções:</strong> Entendo que a entrega do certificado fora desta data não é garantida, exceto em casos excepcionais devidamente comprovados mediante apresentação de atestado médico ou justificativa legal.</li>
                <li><strong>Via Única:</strong> Responsabilizo-me pela guarda e preservação do meu certificado original, estando ciente de que o projeto não emitirá segunda via em caso de perda, dano ou extravio por parte do cursista.</li>
            </ul>

            <h4>3. Declaração de Aceite</h4>
            <p>Ao clicar no botão "Aceito os Termos" abaixo, confirmo que li, compreendi e aceito integralmente as condições estabelecidas acima para a minha participação nesta ação extensionista.</p>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('inscricaoTermsModalStandalone')" class="btn btn-primary">Fechar</button>
        </div>
    </div>
</div>

<footer>
    <div class="container">
        <p>© <?= date('Y') ?> Forma Muana. Feito com <i class="fas fa-heart" style="color: #fab1a0;"></i> para o seu futuro.</p>
    </div>
</footer>

<script>
    // --- Lógica do Modal ---
    let openModals = [];

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.style.display = "block";
        
        if (openModals.length === 0) {
            document.body.style.overflow = "hidden";
        }
        
        openModals.push(modalId);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.style.display = "none";
        
        openModals = openModals.filter(id => id !== modalId);

        if (openModals.length === 0) {
            document.body.style.overflow = "auto";
        }
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    };
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