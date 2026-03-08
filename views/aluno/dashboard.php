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
    <title>Forma Muana - Painel do Aluno</title>
    <link rel="stylesheet" href="views/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="container nav-flex">
        <div class="logo">
            <a href="index.php#inicio"><i class="fas fa-graduation-cap"></i> Forma Muana</a>
        </div>
        
        <nav class="nav-links" id="nav-links">
            <a href="index.php#inicio">Início</a>
            <a href="#ofertas">Vagas</a>
            <a href="#cursos">Cursos</a>
            <a href="#equipe">Equipe</a>

            <a href="index.php?action=aluno_profile" class="btn btn-primary" style="margin-left: 15px; padding: 8px 20px;">Meu Perfil</a>
            <a href="index.php?action=logout" class="btn" style="background-color: #c0392b; color: white; padding: 8px 20px;">Sair</a>
        </nav>

        <div class="mobile-toggle" id="mobile-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

    <!-- Hero Slider (Simulando a Home) -->
    <section class="hero-slider">
        <div class="slide active" style="background-image: url('views/assets/img/certificacao.jpeg'); background-color: #222;">
            <div class="container hero-content">
                <h1>Bem-vindo ao Forma Muana</h1>
                <p>Qualificação profissional e oportunidades para o seu futuro.</p>
                <div class="hero-btn-group">
                    <a href="#ofertas" class="btn btn-primary">Ver Cursos</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Ofertas -->
    <section id="ofertas" class="section-padding">
        <div class="container">
            <h2 class="section-title">Vagas</h2>
            <p style="text-align: center; color: var(--text-light); max-width: 600px; margin: 0 auto; margin-bottom: 40px;">
                Nossos cursos são ministrados por especialistas dedicados a proporcionar a melhor formação para você.
            </p>
            
            <?php
            // Ensure we have the model and helpers
            require_once 'models/Oferta.php';
            require_once 'core/helpers.php';
            
            // Fetch offers if not already passed by controller
            if (!isset($ofertas)) {
                $ofertas = Oferta::findAll();
            }
            ?>

            <div class="table-responsive" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
                    <thead>
                        <tr style="background-color: var(--primary-color); color: white; text-align: left;">
                            <th style="padding: 12px 15px;">Nome</th>
                            <th style="padding: 12px 15px;">Descrição</th>
                            <th style="padding: 12px 15px;">Local</th>
                            <th style="padding: 12px 15px;">Vagas</th>
                            <th style="padding: 12px 15px;">Carga Horária</th>
                            <th style="padding: 12px 15px;">Público Alvo</th>
                            <th style="padding: 12px 15px;">Inscrições Até</th>
                            <th style="padding: 12px 15px;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ofertas)): ?>
                            <?php foreach ($ofertas as $oferta): ?>
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $encerrado = ($oferta->getFinalInsc() < $hoje);
                                ?>
                                <tr style="border-bottom: 1px solid #dddddd;">
                                    <td style="padding: 12px 15px; font-weight: bold;"><?= htmlspecialchars($oferta->getNome()) ?></td>
                                    <td style="padding: 12px 15px;"><?= htmlspecialchars(substr($oferta->getDescricao(), 0, 50)) . (strlen($oferta->getDescricao()) > 50 ? '...' : '') ?></td>
                                    <td style="padding: 12px 15px;"><?= htmlspecialchars($oferta->getLocal()) ?></td>
                                    <td style="padding: 12px 15px;"><?= htmlspecialchars($oferta->getNumeroVagas()) ?></td>
                                    <td style="padding: 12px 15px;"><?= htmlspecialchars($oferta->getCargaHoraria()) ?>h</td>
                                    <td style="padding: 12px 15px;"><?= htmlspecialchars($oferta->getPublicoAlvo()) ?></td>
                                    <td style="padding: 12px 15px;"><?= date('d/m/Y', strtotime($oferta->getFinalInsc())) ?></td>
                                    <td style="padding: 12px 15px;">
                                        <?php if ($encerrado): ?>
                                            <span style="color: #e74c3c; font-weight: bold;">Encerrado</span>
                                        <?php else: ?>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <?php if (in_array($oferta->getId(), $ofertasInscritasIds)): ?>
                                                    <span style="color: #2ecc71; font-weight: bold;">Inscrito</span>
                                                <?php else: ?>
                                                    <button onclick="openInscricaoModal('<?= $oferta->getId() ?>')" class="btn btn-success" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button onclick="alert('Você precisa efetuar login para efetuar sua inscrição.')" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="padding: 15px; text-align: center;">Nenhuma oferta disponível no momento.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section id="cursos" class="section-padding">
        <div class="container">
            <h2 class="section-title">Nossos Cursos</h2>
            <p style="text-align: center; color: var(--text-light); max-width: 600px; margin: 0 auto; margin-bottom: 40px;">
                Nossos cursos são ministrados por especialistas dedicados a proporcionar a melhor formação para você.
            </p>
            
            <div class="team-grid2">
                
                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/informatica.png" alt="Curso 1">
                    </div>
                    <span class="role-tag">Curso</span>
                    <h3>Informática</h3>
                    <p>Aprenda a criar sites e aplicações web do zero com as tecnologias mais recentes do mercado.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/tecnologias.png" alt="Curso 2">
                    </div>
                    <span class="role-tag">Curso</span>
                    <h3>Tecnologias Digitais</h3>
                    <p>Domine as metodologias ágeis e tradicionais para gerenciar projetos de sucesso.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/libras.png" class="img-centered" alt="Curso 3">
                    </div>
                    <span class="role-tag">Curso</span>
                    <h3>Libras</h3>
                    <p>Aprenda a criar campanhas de marketing digital eficazes e a gerar resultados para empresas.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/assistente.png" class="img-centered" alt="Curso 4">
                    </div>
                    <span class="role-tag">Curso</span>
                    <h3>Assistente Administrativo</h3>
                    <p>Aprenda a criar campanhas de marketing digital eficazes e a gerar resultados para empresas.</p>
                </div>

            </div>
        </div>
    </section>

    <section id="equipe" class="section-padding">
        <div class="container">
            <h2 class="section-title">Nossos Cursos</h2>
            <p style="text-align: center; color: var(--text-light); max-width: 600px; margin: 0 auto;">
                Nossos cursos são ministrados por especialistas dedicados a proporcionar a melhor formação para você.
            </p>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/dentista-8.jpg" alt="Curso 1">
                    </div>
                    <span class="role-tag">Prof. Dr. Fabricio Farias</span>
                    <h3>Coordenador do Projeto</h3>
                    <p>Aprenda a criar sites e aplicações web do zero com as tecnologias mais recentes do mercado.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/dentista-2.jpeg" alt="Curso 2">
                    </div>
                    <span class="role-tag">Prof. Dr. Allan Costa</span>
                    <h3>Pesquisador</h3>
                    <p>Domine as metodologias ágeis e tradicionais para gerenciar projetos de sucesso.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/dentista-7.jpeg" class="img-centered" alt="Curso 3">
                    </div>
                    <span class="role-tag">Prof. Daniel Sidônio</span>
                    <h3>Coordenação Local</h3>
                    <p>Aprenda a criar campanhas de marketing digital eficazes e a gerar resultados para empresas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Contato/Localização -->
    <section class="location-section section-padding">
        <div class="container">
            <div class="section-title">
                <h2>Onde Estamos</h2>
            </div>
            <div class="contact-grid">
                <div class="contact-info-box">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Endereço</h4>
                            <p>Rua Exemplo, 123 - Centro<br>Cidade - UF</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>contato@formamuana.com.br</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Telefone</h4>
                            <p>(00) 1234-5678</p>
                        </div>
                    </div>
                </div>
                <div class="map-frame">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.9537353153169!3d-37.816279742021665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d22e4231b1b1!2sFederation%20Square!5e0!3m2!1sen!2sau!4v1614131686749!5m2!1sen!2sau" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal de Complemento de Cadastro -->
    <div id="completeProfileModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-modal" onclick="closeModal('completeProfileModal')">&times;</span>
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Completar Cadastro</h2>
            </div>
            <div class="modal-body modal-body-scrollable">
                <?php
                $aluno = $aluno ?? [];
                $formAction = 'index.php?action=register_aluno';
                $btnText = 'Concluir Cadastro';
                ?>
                <form action="<?= htmlspecialchars($formAction) ?>" method="POST">
                    <?php // Conteúdo de views/aluno/register.php ?>
                    <fieldset>
                        <legend>Dados Pessoais</legend>
                        <div class="form-group"><label for="nome">Nome Completo</label><input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome'] ?? '') ?>" required class="form-control"></div>
                        <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="whatsapp">WhatsApp</label><input type="text" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($aluno['whatsapp'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="telefone">Outro Telefone</label><input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($aluno['telefone'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="data_nascimento">Data de Nascimento</label><input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($aluno['data_nascimento'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="estado_civil">Estado Civil</label><select id="estado_civil" name="estado_civil" class="form-control"><option value="">Selecione...</option><option value="Solteiro(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Solteiro(a)') ? 'selected' : '' ?>>Solteiro(a)</option><option value="Casado(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Casado(a)') ? 'selected' : '' ?>>Casado(a)</option><option value="Divorciado(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Divorciado(a)') ? 'selected' : '' ?>>Divorciado(a)</option><option value="Viúvo(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Viúvo(a)') ? 'selected' : '' ?>>Viúvo(a)</option><option value="União Estável" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'União Estável') ? 'selected' : '' ?>>União Estável</option><option value="Não informado" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option></select></div>
                    </fieldset>
                    <fieldset><legend>Identidade e Gênero</legend>
                        <div class="form-group"><label for="sexo">Sexo</label><select id="sexo" name="sexo" class="form-control"><option value="">Selecione...</option><option value="Masculino" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option><option value="Feminino" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option><option value="Não declarado" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Não declarado') ? 'selected' : '' ?>>Não declarado</option></select></div>
                        <div class="form-group"><label for="identidade_genero">Identidade de Gênero</label><select id="identidade_genero" name="identidade_genero" class="form-control"><option value="">Selecione...</option><option value="Homem Cis" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Homem Cis') ? 'selected' : '' ?>>Homem Cis</option><option value="Mulher Cis" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Mulher Cis') ? 'selected' : '' ?>>Mulher Cis</option><option value="Homem Trans" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Homem Trans') ? 'selected' : '' ?>>Homem Trans</option><option value="Mulher Trans" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Mulher Trans') ? 'selected' : '' ?>>Mulher Trans</option><option value="Não-binário" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Não-binário') ? 'selected' : '' ?>>Não-binário</option><option value="Outro" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option><option value="Não informado" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option></select></div>
                        <div class="form-group"><label for="possui_nome_social">Possui Nome Social?</label><select id="possui_nome_social" name="possui_nome_social" class="form-control"><option value="Nao" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Nao') ? 'selected' : '' ?>>Não</option><option value="Sim" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Sim') ? 'selected' : '' ?>>Sim</option></select></div>
                        <div class="form-group" id="nome_social_group"><label for="nome_social">Nome Social</label><input type="text" id="nome_social" name="nome_social" value="<?= htmlspecialchars($aluno['nome_social'] ?? '') ?>" class="form-control"></div>
                    </fieldset>
                    <fieldset><legend>Questões Sociais e Étnicas</legend>
                        <div class="form-group"><label for="cor_raca">Cor/Raça</label><select id="cor_raca" name="cor_raca" class="form-control"><option value="">Selecione...</option><option value="Branca" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Branca') ? 'selected' : '' ?>>Branca</option><option value="Preta" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Preta') ? 'selected' : '' ?>>Preta</option><option value="Parda" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Parda') ? 'selected' : '' ?>>Parda</option><option value="Amarela" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Amarela') ? 'selected' : '' ?>>Amarela</option><option value="Indígena" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Indígena') ? 'selected' : '' ?>>Indígena</option><option value="Não declarado" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Não declarado') ? 'selected' : '' ?>>Não declarado</option></select></div>
                        <div class="form-group"><label for="possui_deficiencia">Possui Deficiência?</label><select id="possui_deficiencia" name="possui_deficiencia" class="form-control"><option value="Nao" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Nao') ? 'selected' : '' ?>>Não</option><option value="Sim" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Sim') ? 'selected' : '' ?>>Sim</option></select></div>
                        <div class="form-group" id="tipo_deficiencia_group"><label for="tipo_deficiencia">Tipo da Deficiência</label><input type="text" id="tipo_deficiencia" name="tipo_deficiencia" value="<?= htmlspecialchars($aluno['tipo_deficiencia'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="comunidade_tradicional">Pertence a Comunidade Tradicional?</label><select id="comunidade_tradicional" name="comunidade_tradicional" class="form-control"><option value="">Selecione...</option><option value="Nenhuma" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Nenhuma') ? 'selected' : '' ?>>Nenhuma</option><option value="Quilombola" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Quilombola') ? 'selected' : '' ?>>Quilombola</option><option value="Indígena" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Indígena') ? 'selected' : '' ?>>Indígena</option><option value="Ribeirinha" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Ribeirinha') ? 'selected' : '' ?>>Ribeirinha</option><option value="Outra" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Outra') ? 'selected' : '' ?>>Outra</option></select></div>
                    </fieldset>
                    <fieldset><legend>Residência</legend>
                        <div class="form-group"><label for="zona_residencia">Zona de Residência</label><select id="zona_residencia" name="zona_residencia" class="form-control"><option value="">Selecione...</option><option value="Urbana" <?= (isset($aluno['zona_residencia']) && $aluno['zona_residencia'] == 'Urbana') ? 'selected' : '' ?>>Urbana</option><option value="Rural" <?= (isset($aluno['zona_residencia']) && $aluno['zona_residencia'] == 'Rural') ? 'selected' : '' ?>>Rural</option></select></div>
                        <div class="form-group"><label for="cidade_origem">Cidade de Origem</label><input type="text" id="cidade_origem" name="cidade_origem" value="<?= htmlspecialchars($aluno['cidade_origem'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="cidade_atual">Cidade Atual</label><input type="text" id="cidade_atual" name="cidade_atual" value="<?= htmlspecialchars($aluno['cidade_atual'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="bairro_comunidade">Bairro/Comunidade</label><input type="text" id="bairro_comunidade" name="bairro_comunidade" value="<?= htmlspecialchars($aluno['bairro_comunidade'] ?? '') ?>" class="form-control"></div>
                    </fieldset>
                    <fieldset><legend>Família e Escolaridade</legend>
                        <div class="form-group"><label for="possui_filhos">Possui Filhos?</label><select id="possui_filhos" name="possui_filhos" class="form-control"><option value="Nao" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Nao') ? 'selected' : '' ?>>Não</option><option value="Sim" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Sim') ? 'selected' : '' ?>>Sim</option></select></div>
                        <div class="form-group" id="quantidade_filhos_group"><label for="quantidade_filhos">Quantidade de Filhos</label><input type="number" id="quantidade_filhos" name="quantidade_filhos" min="0" value="<?= htmlspecialchars($aluno['quantidade_filhos'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="nivel_escolaridade">Nível de Escolaridade</label><select id="nivel_escolaridade" name="nivel_escolaridade" class="form-control"><option value="">Selecione...</option><option value="Ensino Fundamental Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Fundamental Incompleto') ? 'selected' : '' ?>>Ensino Fundamental Incompleto</option><option value="Ensino Fundamental Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Fundamental Completo') ? 'selected' : '' ?>>Ensino Fundamental Completo</option><option value="Ensino Médio Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Médio Incompleto') ? 'selected' : '' ?>>Ensino Médio Incompleto</option><option value="Ensino Médio Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Médio Completo') ? 'selected' : '' ?>>Ensino Médio Completo</option><option value="Ensino Superior Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Superior Incompleto') ? 'selected' : '' ?>>Ensino Superior Incompleto</option><option value="Ensino Superior Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Superior Completo') ? 'selected' : '' ?>>Ensino Superior Completo</option><option value="Pós-graduação" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Pós-graduação') ? 'selected' : '' ?>>Pós-graduação</option><option value="Não informado" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option></select></div>
                        <div class="form-group"><label for="tipo_escola_ensino_medio">Tipo de Escola no Ensino Médio</label><select id="tipo_escola_ensino_medio" name="tipo_escola_ensino_medio" class="form-control"><option value="">Selecione...</option><option value="Pública" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Pública') ? 'selected' : '' ?>>Pública</option><option value="Particular" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Particular') ? 'selected' : '' ?>>Particular</option><option value="Bolsista em escola particular" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Bolsista em escola particular') ? 'selected' : '' ?>>Bolsista em escola particular</option><option value="Outro" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Outro') ? 'selected' : '' ?>>Outro</option><option value="Não se aplica" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Não se aplica') ? 'selected' : '' ?>>Não se aplica</option></select></div>
                    </fieldset>
                    <fieldset><legend>Informações Socioeconômicas</legend>
                        <div class="form-group"><label for="beneficiario_programa_social">É beneficiário de programa social?</label><select id="beneficiario_programa_social" name="beneficiario_programa_social" class="form-control"><option value="Nao" <?= (isset($aluno['beneficiario_programa_social']) && $aluno['beneficiario_programa_social'] == 'Nao') ? 'selected' : '' ?>>Não</option><option value="Sim" <?= (isset($aluno['beneficiario_programa_social']) && $aluno['beneficiario_programa_social'] == 'Sim') ? 'selected' : '' ?>>Sim</option></select></div>
                        <div class="form-group" id="nome_programa_social_group"><label for="nome_programa_social">Qual programa?</label><input type="text" id="nome_programa_social" name="nome_programa_social" value="<?= htmlspecialchars($aluno['nome_programa_social'] ?? '') ?>" class="form-control"></div>
                        <div class="form-group"><label for="acesso_internet">Tipo de Acesso à Internet</label><select id="acesso_internet" name="acesso_internet" class="form-control"><option value="">Selecione...</option><option value="Banda Larga Residencial" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Banda Larga Residencial') ? 'selected' : '' ?>>Banda Larga Residencial</option><option value="Dados Móveis" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Dados Móveis') ? 'selected' : '' ?>>Dados Móveis</option><option value="Internet via Rádio" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Internet via Rádio') ? 'selected' : '' ?>>Internet via Rádio</option><option value="Não possui acesso" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Não possui acesso') ? 'selected' : '' ?>>Não possui acesso</option><option value="Outro" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Outro') ? 'selected' : '' ?>>Outro</option></select></div>
                        <div class="form-group"><label for="dispositivo_principal">Dispositivo Principal de Acesso</label><select id="dispositivo_principal" name="dispositivo_principal" class="form-control"><option value="">Selecione...</option><option value="Celular" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Celular') ? 'selected' : '' ?>>Celular</option><option value="Computador (Desktop)" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Computador (Desktop)') ? 'selected' : '' ?>>Computador (Desktop)</option><option value="Notebook" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Notebook') ? 'selected' : '' ?>>Notebook</option><option value="Tablet" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Tablet') ? 'selected' : '' ?>>Tablet</option><option value="Outro" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Outro') ? 'selected' : '' ?>>Outro</option></select></div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;"><?= $btnText ?></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Inscrição na Oferta -->
    <div id="inscricaoOfertaModal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-modal" onclick="closeModal('inscricaoOfertaModal')">&times;</span>
            <div class="modal-header">
                <h2><i class="fas fa-graduation-cap"></i> Formulário de Inscrição na Oferta</h2>
            </div>
            <div class="modal-body modal-body-scrollable">
                <form action="" method="POST"> <?php // Action é definida via JS ?>
                    <fieldset>
                        <legend>Informações Socioeconômicas</legend>
                        <div class="form-group">
                            <label for="exerce_atividade_remunerada_modal">Exerce alguma atividade remunerada?</label>
                            <select id="exerce_atividade_remunerada_modal" name="exerce_atividade_remunerada" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Sim">Sim</option>
                                <option value="Nao">Não</option>
                                <option value="As_vezes">Às vezes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vinculo_atividade_modal">Se sim, qual o seu vínculo ou tipo de atividade?</label>
                            <input type="text" id="vinculo_atividade_modal" name="vinculo_atividade" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="afinidade_atividade_curso_modal">Sua atividade remunerada tem afinidade com a área do curso?</label>
                            <select id="afinidade_atividade_curso_modal" name="afinidade_atividade_curso" class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Sim">Sim</option>
                                <option value="Nao">Não</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="participacao_economica_familiar_modal">Qual sua principal participação na vida econômica familiar?</label>
                            <select id="participacao_economica_familiar_modal" name="participacao_economica_familiar" class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                                <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                                <option value="Nao_contribuo">Não contribuo com as despesas</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="numero_membros_familia_modal">Quantas pessoas, incluindo você, moram em sua residência?</label>
                            <select id="numero_membros_familia_modal" name="numero_membros_familia" class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <?php for ($i = 1; $i <= 20; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renda_familiar_modal">Qual a renda total da sua família (incluindo você)?</label>
                            <select id="renda_familiar_modal" name="renda_familiar" class="form-control">
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
                        <div class="form-group"><label for="motivo_escolha_curso_modal">Por que você escolheu este curso? Quais habilidades espera desenvolver?</label><textarea id="motivo_escolha_curso_modal" name="motivo_escolha_curso" rows="4" class="form-control"></textarea></div>
                        <div class="form-group"><label for="o_que_mais_espera_do_curso_modal">O que você mais espera do curso?</label><textarea id="o_que_mais_espera_do_curso_modal" name="o_que_mais_espera_do_curso" rows="4" class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_relacao_curso_modal">Quais suas expectativas em relação ao curso?</label><textarea id="expectativas_relacao_curso_modal" name="expectativas_relacao_curso" rows="4" class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_profissionais_pos_curso_modal">Quais suas expectativas profissionais após a conclusão do curso?</label><textarea id="expectativas_profissionais_pos_curso_modal" name="expectativas_profissionais_pos_curso" rows="4" class="form-control"></textarea></div>
                        <div class="form-group"><label for="canal_informacao_curso_modal">Como você ficou sabendo deste curso?</label><select id="canal_informacao_curso_modal" name="canal_informacao_curso" class="form-control"><option value="" disabled selected>Selecione</option><option value="Redes_sociais">Redes Sociais (Instagram, Facebook, etc.)</option><option value="Amigos_familiares">Indicação de amigos ou familiares</option><option value="Site_instituicao">Site da Instituição</option><option value="Busca_internet">Busca na Internet (Google, etc.)</option><option value="Radio_tv">Rádio ou TV</option><option value="Outro">Outro</option></select></div>
                        <div class="form-group"><label for="turno_preferencia_modal">Qual o seu turno de preferência para estudar?</label><select id="turno_preferencia_modal" name="turno_preferencia" class="form-control"><option value="" disabled selected>Selecione</option><option value="Manha">Manhã</option><option value="Tarde">Tarde</option><option value="Noite">Noite</option><option value="Indiferente">Indiferente</option></select></div>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Termo de Aceite</legend>
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" id="termo_aceite_insc_modal" name="termo_aceite" value="Sim" required style="width: auto;">
                            <label for="termo_aceite_insc_modal" style="margin-bottom: 0;">Você concorda com os termos desse projeto?</label>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;">Finalizar Inscrição</button>
                </form>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

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

        // --- Lógica Específica da Página ---

        function openInscricaoModal(ofertaId) {
            const modal = document.getElementById('inscricaoOfertaModal');
            if (!modal) return;
            
            const form = modal.querySelector('form');
            form.action = `index.php?action=do_inscricao_oferta&oferta_id=${ofertaId}`;
            
            openModal('inscricaoOfertaModal');
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($openCompleteProfileModal) && $openCompleteProfileModal): ?>
                openModal('completeProfileModal');
                
                const msgDiv = document.createElement('div');
                msgDiv.style = 'background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ffeeba;';
                msgDiv.innerHTML = '<i class="fas fa-info-circle"></i> Por favor, complete seu cadastro para acessar todas as funcionalidades.';
                const modalBody = document.querySelector('#completeProfileModal .modal-body');
                if (modalBody) {
                    modalBody.insertBefore(msgDiv, modalBody.firstChild);
                }
            <?php endif; ?>

            // Script para campos condicionais do formulário de perfil
            function toggleVisibility(selectElement, targetGroup, showValue) {
                if (!selectElement || !targetGroup) return;
                function checkVisibility() {
                    targetGroup.style.display = (selectElement.value === showValue) ? '' : 'none';
                }
                checkVisibility();
                selectElement.addEventListener('change', checkVisibility);
            }

            toggleVisibility(document.getElementById('possui_nome_social'), document.getElementById('nome_social_group'), 'Sim');
            toggleVisibility(document.getElementById('possui_deficiencia'), document.getElementById('tipo_deficiencia_group'), 'Sim');
            toggleVisibility(document.getElementById('possui_filhos'), document.getElementById('quantidade_filhos_group'), 'Sim');
            toggleVisibility(document.getElementById('beneficiario_programa_social'), document.getElementById('nome_programa_social_group'), 'Sim');
        });

        // --- Lógica do Menu Mobile ---
        const mobileToggle = document.getElementById('mobile-toggle');
        const navLinks = document.getElementById('nav-links');

        if (mobileToggle) {
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
        }
    </script>
</body>
</html>