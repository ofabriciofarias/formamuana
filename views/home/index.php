<?php
// Define a BASE_URL se não estiver definida.
if (!defined('BASE_URL')) define('BASE_URL', '/formamuana/'); 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma Muana | Qualificação Profissional</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="views/assets/css/style.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo">
                <a href="#"> Forma Muana</a>
            </div>
            
            <div class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="nav-links" id="nav-links">
                <a href="#inicio">Início</a>
                <a href="#especialistas">Cursos</a>
                <a href="#localizacao">Localização</a>
                
                <form action="index.php?action=do_login" method="POST" class="header-login-form">
                    <input type="text" name="login" placeholder="Usuário" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
                </form>
            </nav>
        </div>
    </header>

    <?php if(isset($_GET['error'])): ?>
        <div style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1100; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fas fa-exclamation-circle"></i> Dados de acesso inválidos.
        </div>
    <?php endif; ?>

    <section id="inicio" class="hero-slider">
        <div class="slide active" style="background-image: url('views/assets/img/dentista-5.jpeg');"></div>
        <div class="slide" style="background-image: url('views/assets/img/cadeira.jpeg'); filter: hue-rotate(20deg);"></div> 
        <div class="slide" style="background-image: url('views/assets/img/card1.jpg'); background-position: center;"></div>

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
                    <a href="index.php?action=register" class="btn btn-success btn-lg">
                        <i class="fas fa-graduation-cap"></i> Inscreva-se Agora
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="especialistas" class="section-padding">
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
                    <span class="role-tag">Tecnologia</span>
                    <h3>Desenvolvimento Web</h3>
                    <p>Aprenda a criar sites e aplicações web do zero com as tecnologias mais recentes do mercado.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/dentista-2.jpeg" alt="Curso 2">
                    </div>
                    <span class="role-tag">Gestão</span>
                    <h3>Gestão de Projetos</h3>
                    <p>Domine as metodologias ágeis e tradicionais para gerenciar projetos de sucesso.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/dentista-7.jpeg" class="img-centered" alt="Curso 3">
                    </div>
                    <span class="role-tag">Marketing</span>
                    <h3>Marketing Digital</h3>
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
    </script>
</body>
</html>
