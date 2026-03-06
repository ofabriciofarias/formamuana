<?php require_once 'views/layouts/public_header.php'; ?>

    <?php if(isset($_GET['error'])): ?>
        <div style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1100; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fas fa-exclamation-circle"></i> Usuário não encontrado ou senha incorreta. Tente novamente ou cadastre-se.
        </div>
    <?php endif; ?>

    <section id="inicio" class="hero-slider">
        <div class="slide active" style="background-image: url('views/assets/img/certificacao.jpeg');"></div>
        <div class="slide" style="background-image: url('views/assets/img/certificacao4.jpg'); filter: hue-rotate(20deg);"></div> 
        <div class="slide" style="background-image: url('views/assets/img/aula5.jpg'); background-position: center;"></div>

        <div class="container hero-content">
            <div class="hero-text-wrap">
                <h1>Qualifique seu futuro<br>com nossos cursos!</h1>
                <p>Na <strong>Forma Muana</strong>, unimos tecnologia de ponta e ensino humanizado para impulsionar sua carreira.</p>
                
                <div class="hero-features">
                    <span class="hero-tag"><i class="fas fa-check-circle"></i> Cursos Práticos</span>
                    <span class="hero-tag"><i class="fas fa-wifi"></i> Estrutura Moderna</span>
                    <span class="hero-tag"><i class="far fa-credit-card"></i> Flexibilidade</span>
                </div>

                <div class="hero-btn-group">
                    <a href="#" onclick="openModal('registerModal'); return false;" class="btn btn-success btn-lg">
                        <i class="fas fa-graduation-cap"></i> Inscreva-se Agora
                    </a>
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
                                                <a href="index.php?action=show_inscricao_oferta_form&oferta_id=<?= $oferta->getId() ?>" class="btn btn-success" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</a>
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

    <section id="localizacao" class="section-padding location-section">
        <div class="container">
            <h2 class="section-title">Onde Estamos</h2>
            
            <div class="contact-grid">
                <div class="contact-info-box">
                    <h3>Visite-nos</h3>
                    <p style="margin-bottom: 20px; color: var(--text-light);">Localização privilegiada com fácil acesso.</p>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Endereço:</strong><br>
                            Sua Rua, 123<br>
                            Seu Bairro, Sua Cidade - UF
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <strong>Telefone / WhatsApp:</strong><br>
                            (99) 99999-9999
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="far fa-clock"></i>
                        <div>
                            <strong>Horário de Atendimento:</strong><br>
                            Seg - Sex: 08h às 18h
                        </div>
                    </div>
                </div>

                <div class="map-frame">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.645365860427!2d-48.42899942535724!3d-1.3893519985975165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x92a48b002f32a7cf%3A0x353ede76a35e88fa!2sPrev%20Dentistas!5e0!3m2!1sen!2sbr!4v1771775822488!5m2!1sen!2sbr" 
                        width="600" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© <?= date('Y') ?> Forma Muana. Feito com <i class="fas fa-heart" style="color: #fab1a0;"></i> para o seu futuro.</p>
        </div>
    </footer>

    <a href="https://wa.me/5591983067459" class="fab-whatsapp" target="_blank" title="Fale conosco no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Modal de Cadastro -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('registerModal')">&times;</span>
            <h2 style="text-align: center; margin-bottom: 20px;">Cadastro de Aluno</h2>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">Preencha os dados abaixo para criar sua conta.</p>
            
            <form action="index.php?action=register" method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="login">Login (Usuário)</label>
                    <input type="text" id="login" name="login" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="telefone">Telefone / WhatsApp</label>
                    <input type="text" id="telefone" name="telefone" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" required class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                
                <!-- Campo oculto para Tipo de Usuário (Aluno = 3) -->
                <input type="hidden" name="tipo_usuario_id" value="3">

                <div class="form-group" style="margin-top: 15px;">
                    <input type="checkbox" id="termo_aceite_modal" name="termo_aceite" value="aceito" required>
                    <label for="termo_aceite_modal" style="display:inline; font-weight: normal;">Eu li e aceito os termos de uso.</label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Cadastrar</button>
            </form>
        </div>
    </div>

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

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                if(!link.closest('form')) { 
                    navLinks.classList.remove('active');
                    mobileToggle.querySelector('i').classList.remove('fa-times');
                    mobileToggle.querySelector('i').classList.add('fa-bars');
                }
            });
        });

        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const totalSlides = slides.length;

        function nextSlide() {
            if (slides.length > 0) {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % totalSlides;
                slides[currentSlide].classList.add('active');
            }
        }

        setInterval(nextSlide, 5000);

        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if(window.scrollY > 50) {
                header.style.boxShadow = "0 5px 20px rgba(0,0,0,0.1)";
                header.style.padding = "10px 0";
            } else {
                header.style.boxShadow = "0 2px 10px rgba(0,0,0,0.05)";
                header.style.padding = "15px 0";
            }
        });

        // Funções do Modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
            document.body.style.overflow = "hidden"; // Evita rolagem do fundo
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            document.body.style.overflow = "auto"; // Restaura rolagem
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = "auto";
            }
        }
    </script>
</body>
</html>
