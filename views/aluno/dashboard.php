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
                                                    <a href="index.php?action=show_inscricao_oferta_form&oferta_id=<?= $oferta->getId() ?>" class="btn btn-success" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</a>
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

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>