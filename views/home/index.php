<?php require_once 'views/layouts/public_header.php'; ?>

    <section id="inicio" class="hero-slider">
        <div class="slide active" style="background-image: url('views/assets/img/certificacao.jpeg');"></div>
        <div class="slide" style="background-image: url('views/assets/img/certificacao4.jpg'); filter: hue-rotate(20deg);"></div> 
        <div class="slide" style="background-image: url('views/assets/img/aula5.jpg'); background-position: center;"></div>

        <div class="container hero-content">
            <div class="hero-text-wrap">
                <h1>Qualifique seu futuro<br>com nossos cursos!</h1>
                <p>No <strong>Forma Muaná</strong>, unimos tecnologia de ponta e ensino humanizado para impulsionar sua carreira.</p>
                
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

            <div class="table-responsive">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Local</th>
                            <th>Vagas</th>
                            <th>Carga Horária</th>
                            <th>Público Alvo</th>
                            <th>Inscrições Até</th>
                            <th>Detalhes</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ofertas)): ?>
                            <?php foreach ($ofertas as $oferta): ?>
                                <?php 
                                    $hoje = date('Y-m-d');
                                    $encerrado = ($oferta['final_insc'] < $hoje);
                                    $detalhes = [
                                        'nome' => $oferta['nome'],
                                        'descricao' => $oferta['descricao'],
                                        'eixo_tecnologico' => $oferta['eixo_tecnologico'],
                                        'foto' => $oferta['foto'],
                                        'inicio_insc' => date('d/m/Y', strtotime($oferta['inicio_insc'])),
                                        'final_insc' => date('d/m/Y', strtotime($oferta['final_insc'])),
                                        'local' => $oferta['local'],
                                        'turno' => $oferta['turno'],
                                        'numero_vagas' => $oferta['numero_vagas'],
                                        'publico_alvo' => $oferta['publico_alvo'],
                                        'modalidade' => $oferta['modalidade'],
                                        'carga_horaria' => $oferta['carga_horaria'],
                                        'inicio_aulas' => !empty($oferta['inicio_aulas']) ? date('d/m/Y', strtotime($oferta['inicio_aulas'])) : null,
                                        'fim_aulas' => !empty($oferta['fim_aulas']) ? date('d/m/Y', strtotime($oferta['fim_aulas'])) : null,
                                        'inicio_hora_aula' => !empty($oferta['inicio_hora_aula']) ? date('H:i', strtotime($oferta['inicio_hora_aula'])) : null,
                                        'fim_hora_aula' => !empty($oferta['fim_hora_aula']) ? date('H:i', strtotime($oferta['fim_hora_aula'])) : null,
                                    ];
                                ?>
                                <tr>
                                    <td data-label="Nome" style="font-weight: bold;"><?= htmlspecialchars($oferta['nome']) ?></td>
                                    <td data-label="Descrição"><?= htmlspecialchars(substr($oferta['descricao'], 0, 50)) . (strlen($oferta['descricao']) > 50 ? '...' : '') ?></td>
                                    <td data-label="Local"><?= htmlspecialchars($oferta['local']) ?></td>
                                    <td data-label="Vagas"><?= htmlspecialchars($oferta['numero_vagas']) ?></td>
                                    <td data-label="Carga Horária"><?= htmlspecialchars($oferta['carga_horaria']) ?>h</td>
                                    <td data-label="Público Alvo"><?= htmlspecialchars($oferta['publico_alvo']) ?></td>
                                    <td data-label="Inscrições Até"><?= date('d/m/Y', strtotime($oferta['final_insc'])) ?></td>
                                    <td data-label="Detalhes" style="text-align: center;">
                                        <button onclick='openDetalhesOfertaModal(<?= json_encode($detalhes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)' style="background: none; border: none; color: var(--primary-color); padding: 5px; font-size: 1.1rem; cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td data-label="Ação">
                                        <?php if ($encerrado): ?>
                                            <span style="color: #e74c3c; font-weight: bold;">Encerrado</span>
                                        <?php else: ?>
                                            <?php if (isset($_SESSION['user_id'])): ?>
                                                <button onclick="openInscricaoModal('<?= $oferta['id'] ?>', '<?= $oferta['turno'] ?>')" class="btn btn-success" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</button>
                                            <?php else: ?>
                                                <button onclick="openModal('loginRequiredModal')" class="btn btn-primary" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">Nenhuma oferta disponível no momento.</td>
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
                    <h3>Informática para Inserção no Mercado de Trabalho</h3>
                    <p>Aprenda a criar sites e aplicações web do zero com as tecnologias mais recentes do mercado.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/tecnologias.png" alt="Curso 2">
                    </div>
                    <span class="role-tag">Curso</span>
                    <h3>Tecnologias Digitais na Educação</h3>
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
            <h2 class="section-title">Realização</h2>
            <p style="text-align: center; color: var(--text-light); max-width: 600px; margin: 0 auto;">
                Este projeto é uma iniciativa da SECTET, sendo executado UFPA com suporte da FADESP.
            </p>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/sectet.jpg" alt="Curso 1">
                    </div>
                    <span class="role-tag">SECTET</span>
                    <h3>Agência Financiadora</h3>
                    <p>Secretaria de Ciência, Tecnologia e Educação Superior, Profissional e Tecnológica do Estado do Pará (SECTET)</p>
                    Secretário Victor Dias
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/ufpa.jpeg" alt="Curso 2">
                    </div>
                    <span class="role-tag">UFPA</span>
                    <h3>Execução do Projeto</h3>
                    <p>Universidade Federal do Pará</p>
                    Reitor Prof. Dr. Gilmar Silva
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="views/assets/img/fadesp.png" class="img-centered" alt="Curso 3">
                    </div>
                    <span class="role-tag">FADESP</span>
                    <h3>Suporte Administrativo</h3>
                    <p>Fundação de Amparo e Desenvolvimento de Pesquisa</p>
                    Presidente Prof. Dr. Roberto Ferraz
                </div>
            </div>
        </div>
    </section>

    <!--
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
                    -->
    <footer>
        <div class="container">
            <p>© <?= date('Y') ?> Forma Muaná. Feito com <i class="fas fa-heart" style="color: #fab1a0;"></i> para o seu futuro.</p>
        </div>
    </footer>

    <a href="https://wa.me/5591992220689" class="fab-whatsapp" target="_blank" title="Fale conosco no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Modal de Cadastro -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('registerModal')">&times;</span>
            <div class="modal-header">
                <h2><i class="fas fa-user-plus"></i> Cadastro de Aluno</h2>
            </div>
            <div class="modal-body">
                <p>Preencha os dados abaixo para criar sua conta.</p>
                <form action="index.php?action=register" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="login">Login (Usuário)</label>
                        <input type="text" id="login" name="login" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone / WhatsApp</label>
                        <input type="text" id="telefone" name="telefone" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" name="senha" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required class="form-control">
                    </div>
                    
                    <!-- Campo oculto para Tipo de Usuário (Aluno = 3) -->
                    <input type="hidden" name="tipo_usuario_id" value="3">

                    <div class="form-group">
                        <input type="checkbox" id="termo_aceite_modal" name="termo_aceite" value="aceito" required>
                        <label for="termo_aceite_modal" class="checkbox-label">Eu li e aceito os termos de uso.</label>
                        <a href="#" onclick="event.preventDefault(); openModal('termsModal');" class="terms-link">(Saber mais)</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Termos de Uso -->
    <div id="termsModal" class="modal modal-on-top">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('termsModal')">&times;</span>
            <div class="modal-header">
                <h2>Termo de Ciência e Aceite de Participação</h2>
            </div>
            <div class="modal-body modal-body-scrollable">
                <p>Ao prosseguir com esta inscrição, eu declaro estar de acordo com os termos de livre participação no projeto de formação Forma Muaná, da Universidade Federal do Pará (UFPA), submetendo-me às seguintes condições:</p>

                <h4>1. Veracidade das Informações</h4>
                <p>Declaro que todas as informações prestadas neste formulário são verdadeiras e assumo total responsabilidade por elas. Estou ciente de que a omissão ou a falsidade de dados em documentos públicos constitui crime previsto no Art. 299 do Código Penal Brasileiro, sujeitando o infrator às penalidades legais, além da imediata desclassificação do projeto.</p>

                <h4>2. Critérios de Seleção e Notificação</h4>
                <p>Tenho ciência de que a inscrição não garante a vaga imediata. A seleção será realizada pela coordenação do projeto com base em:</p>
                <ul>
                    <li>Disponibilidade de vagas;</li>
                    <li>Análise de critérios de vulnerabilidade socioeconômica (critério majoritário);</li>
                    <li>Conformidade com o público-alvo do projeto.</li>
                </ul>
                <p>Declaro estar ciente de que receberei a confirmação do aceite ou indeferimento da minha vaga pelos canais de comunicação oficiais do projeto.</p>

                <h4>3. Comunicação e Proteção de Dados (LGPD)</h4>
                <p>Em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018), autorizo expressamente o projeto Forma Muaná a:</p>
                <ul>
                    <li>Utilizar meus dados para fins de seleção, estatística institucional e registro acadêmico;</li>
                    <li>Enviar notificações sobre o status da minha inscrição via e-mail;</li>
                    <li>Realizar comunicações sobre novas ofertas de turmas, eventos e avisos urgentes via e-mail, SMS ou WhatsApp (incluindo a inclusão em grupos oficiais de mensagens associados ao projeto).</li>
                </ul>

                <h4>4. Consentimento</h4>
                <p>Por fim, ao clicar em "Aceito", confirmo que li e compreendi os termos acima, manifestando minha vontade livre e informada de participar do processo de seleção.</p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('termsModal')" class="btn btn-primary">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Login Necessário (Aprimorado) -->
    <div id="loginRequiredModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('loginRequiredModal')">&times;</span>
            <div class="modal-header">
                <h2><i class="fas fa-sign-in-alt"></i>Login Necessário</h2>
            </div>
            <div class="modal-body">
                <p>Você precisa estar logado para realizar sua inscrição.</p>
                <form action="index.php?action=do_login" method="POST" class="modal-login-form">
                    <div class="form-group">
                        <label for="login_modal">Usuário</label>
                        <input type="text" id="login_modal" name="login" required class="form-control" oninvalid="this.setCustomValidity('Preencha este campo com seu login ou email.')" oninput="this.setCustomValidity('')">
                    </div>
                    <div class="form-group">
                        <label for="senha_modal">Senha</label>
                        <input type="password" id="senha_modal" name="senha" required class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                </form>
            </div>
            <div class="modal-footer">
                <p>Não tem uma conta? <a href="#" onclick="closeModal('loginRequiredModal'); openModal('registerModal'); return false;">Cadastre-se</a></p>
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
                            <label for="exerce_atividade_remunerada_modal_home">Exerce alguma atividade remunerada?</label>
                            <select id="exerce_atividade_remunerada_modal_home" name="exerce_atividade_remunerada" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Sim">Sim</option>
                                <option value="Nao">Não</option>
                                <option value="As_vezes">Às vezes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vinculo_atividade_modal_home">Se sim, qual o seu vínculo ou tipo de atividade?</label>
                            <input type="text" id="vinculo_atividade_modal_home" name="vinculo_atividade" class="form-control"> <!-- required é gerenciado por JS -->
                        </div>
                        <div class="form-group">
                            <label for="afinidade_atividade_curso_modal_home">Sua atividade remunerada tem afinidade com a área do curso?</label>
                            <select id="afinidade_atividade_curso_modal_home" name="afinidade_atividade_curso" class="form-control"> <!-- required é gerenciado por JS -->
                                <option value="" disabled selected>Selecione</option>
                                <option value="Sim">Sim</option>
                                <option value="Nao">Não</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="participacao_economica_familiar_modal_home">Qual sua principal participação na vida econômica familiar?</label>
                            <select id="participacao_economica_familiar_modal_home" name="participacao_economica_familiar" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                                <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                                <option value="Nao_contribuo">Não contribuo com as despesas</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="numero_membros_familia_modal_home">Quantas pessoas, incluindo você, moram em sua residência?</label>
                            <select id="numero_membros_familia_modal_home" name="numero_membros_familia" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <?php for ($i = 1; $i <= 20; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renda_familiar_modal_home">Qual a renda total da sua família (incluindo você)?</label>
                            <select id="renda_familiar_modal_home" name="renda_familiar" required class="form-control">
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
                        <div class="form-group"><label for="motivo_escolha_curso_modal_home">Por que você escolheu este curso? Quais habilidades espera desenvolver?</label><textarea id="motivo_escolha_curso_modal_home" name="motivo_escolha_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="o_que_mais_espera_do_curso_modal_home">O que você mais espera do curso?</label><textarea id="o_que_mais_espera_do_curso_modal_home" name="o_que_mais_espera_do_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_relacao_curso_modal_home">Quais suas expectativas em relação ao curso?</label><textarea id="expectativas_relacao_curso_modal_home" name="expectativas_relacao_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_profissionais_pos_curso_modal_home">Quais suas expectativas profissionais após a conclusão do curso?</label><textarea id="expectativas_profissionais_pos_curso_modal_home" name="expectativas_profissionais_pos_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="canal_informacao_curso_modal_home">Como você ficou sabendo deste curso?</label><select id="canal_informacao_curso_modal_home" name="canal_informacao_curso" required class="form-control"><option value="" disabled selected>Selecione</option><option value="Redes_sociais">Redes Sociais (Instagram, Facebook, etc.)</option><option value="Amigos_familiares">Indicação de amigos ou familiares</option><option value="Site_instituicao">Site da Instituição</option><option value="Busca_internet">Busca na Internet (Google, etc.)</option><option value="Radio_tv">Rádio ou TV</option><option value="Outro">Outro</option></select></div>
                        <div class="form-group"><label for="turno_preferencia_modal_home">Qual o seu turno de preferência para estudar?</label><input type="text" id="turno_preferencia_modal_home" name="turno_preferencia" class="form-control" readonly></div>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Termo de Aceite</legend>
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" id="termo_aceite_insc_modal_home" name="termo_aceite" value="Sim" required style="width: auto;"/>
                            <label for="termo_aceite_insc_modal_home" style="margin-bottom: 0;">Li e concordo com os termos.</label>
                            <a href="#" onclick="event.preventDefault(); openModal('inscricaoTermsModalHome');" class="terms-link">(Saber mais)</a>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;">Finalizar Inscrição</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Termos de Inscrição -->
    <div id="inscricaoTermsModalHome" class="modal modal-on-top">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('inscricaoTermsModalHome')">&times;</span>
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
                <button onclick="closeModal('inscricaoTermsModalHome')" class="btn btn-primary">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Detalhes da Oferta -->
    <div id="detalhesOfertaModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <span class="close-modal" onclick="closeModal('detalhesOfertaModal')">&times;</span>
            <div class="modal-header">
                <h2 id="detalhesOfertaNome"></h2>
            </div>
            <div class="modal-body modal-body-scrollable">
                <img id="detalhesOfertaFoto" src="" alt="Foto da Oferta" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; display: none;">
                
                <h4>Descrição Completa</h4>
                <p id="detalhesOfertaDescricao" style="text-align: justify;"></p>

                <h4 style="margin-top: 20px;">Detalhes do Curso</h4>
                <div id="detalhesOfertaLista">
                    <!-- Conteúdo injetado via JS -->
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('detalhesOfertaModal')" class="btn btn-primary">Fechar</button>
            </div>
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

        // --- Lógica do Modal Aprimorada ---
        // Mantém o controle dos modais abertos para gerenciar a rolagem do corpo da página corretamente.
        let openModals = [];

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.style.display = "block";
            
            // Se este for o primeiro modal a ser aberto, bloqueia a rolagem do corpo.
            if (openModals.length === 0) {
                document.body.style.overflow = "hidden";
            }
            
            // Adiciona o modal à pilha de modais abertos.
            openModals.push(modalId);
        }

        function openInscricaoModal(ofertaId, turno) {
            const modal = document.getElementById('inscricaoOfertaModal');
            if (!modal) return;
            
            const form = modal.querySelector('form');
            form.action = `index.php?action=do_inscricao_oferta&oferta_id=${ofertaId}`;
            
            const turnoSelect = document.getElementById('turno_preferencia_modal_home');
            if(turnoSelect) turnoSelect.value = turno;

            openModal('inscricaoOfertaModal');
        }

        function openDetalhesOfertaModal(oferta) {
            document.getElementById('detalhesOfertaNome').innerText = oferta.nome;
            document.getElementById('detalhesOfertaDescricao').innerText = oferta.descricao;

            const fotoEl = document.getElementById('detalhesOfertaFoto');
            if (oferta.foto && oferta.foto.trim() !== '') {
                fotoEl.src = oferta.foto;
                fotoEl.style.display = 'block';
            } else {
                fotoEl.style.display = 'none';
            }

            const detalhesLista = document.getElementById('detalhesOfertaLista');
            detalhesLista.innerHTML = `
                <div class="info-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div><strong>Período de Inscrição:</strong><br>${oferta.inicio_insc} até ${oferta.final_insc}</div>
                </div>
                ${oferta.inicio_aulas ? `
                <div class="info-item">
                    <i class="fas fa-calendar-check"></i>
                    <div><strong>Período das Aulas:</strong><br>De ${oferta.inicio_aulas} até ${oferta.fim_aulas || 'a definir'}</div>
                </div>
                ` : ''}
                ${oferta.inicio_hora_aula ? `
                <div class="info-item">
                    <i class="far fa-clock"></i>
                    <div><strong>Horário das Aulas:</strong><br>Das ${oferta.inicio_hora_aula} às ${oferta.fim_hora_aula || 'a definir'}</div>
                </div>
                ` : ''}
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div><strong>Local:</strong><br>${oferta.local || 'Não informado'}</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <div><strong>Público Alvo:</strong><br>${oferta.publico_alvo || 'Não informado'}</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-tag"></i>
                    <div><strong>Modalidade:</strong><br>${oferta.modalidade || 'Não informado'}</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-sun"></i>
                    <div><strong>Turno:</strong><br>${oferta.turno || 'Não informado'}</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-chair"></i>
                    <div><strong>Vagas:</strong><br>${oferta.numero_vagas || 'Não informado'}</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div><strong>Carga Horária:</strong><br>${oferta.carga_horaria}h</div>
                </div>
                <div class="info-item">
                    <i class="fas fa-sitemap"></i>
                    <div><strong>Eixo Tecnológico:</strong><br>${oferta.eixo_tecnologico || 'Não informado'}</div>
                </div>
            `;

            openModal('detalhesOfertaModal');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            modal.style.display = "none";
            
            // Remove o modal da pilha.
            openModals = openModals.filter(id => id !== modalId);

            // Se não houver mais modais abertos, restaura a rolagem do corpo.
            if (openModals.length === 0) {
                document.body.style.overflow = "auto";
            }
        }

        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                // O alvo do evento é o próprio div do modal, que tem o ID.
                closeModal(event.target.id);
            }
        };

        // Ao carregar a página, verifica se há um erro de registro duplicado e abre o modal.
        document.addEventListener('DOMContentLoaded', function() {
            <?php
            if (isset($_SESSION['flash_error']) && isset($_SESSION['open_modal'])) {
                $errorMessage = $_SESSION['flash_error'];
                $modalToOpen = $_SESSION['open_modal'];
                unset($_SESSION['flash_error']);
                unset($_SESSION['open_modal']);
            ?>
                const modalId = '<?= $modalToOpen ?>';
                const errorMessage = '<?= addslashes($errorMessage) ?>';

                openModal(modalId);

                const modal = document.getElementById(modalId);
                if (modal) {
                    const modalBody = modal.querySelector('.modal-body');
                    if (modalBody) {
                        const errorDiv = document.createElement('div');
                        errorDiv.style.background = '#e74c3c';
                        errorDiv.style.color = 'white';
                        errorDiv.style.padding = '15px';
                        errorDiv.style.marginBottom = '20px';
                        errorDiv.style.borderRadius = '5px';
                        errorDiv.style.textAlign = 'center';
                        errorDiv.style.transition = 'opacity 0.5s ease';
                        errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${errorMessage}`;
                        
                        modalBody.insertBefore(errorDiv, modalBody.firstChild);

                        setTimeout(() => {
                            errorDiv.style.opacity = '0';
                            setTimeout(() => errorDiv.remove(), 500);
                        }, 5000); // 5 segundos
                    }
                }
            <?php } ?>
            // Lógica para campos condicionais do formulário de inscrição na home
            const exerceAtividadeSelectHome = document.getElementById('exerce_atividade_remunerada_modal_home');
            if (exerceAtividadeSelectHome) {
                const vinculoInputHome = document.getElementById('vinculo_atividade_modal_home');
                const afinidadeSelectHome = document.getElementById('afinidade_atividade_curso_modal_home');
                const vinculoGroupHome = vinculoInputHome.closest('.form-group');
                const afinidadeGroupHome = afinidadeSelectHome.closest('.form-group');

                function toggleInscricaoFieldsHome() {
                    const isRequired = exerceAtividadeSelectHome.value === 'Sim';
                    vinculoGroupHome.style.display = isRequired ? 'block' : 'none';
                    afinidadeGroupHome.style.display = isRequired ? 'block' : 'none';
                    vinculoInputHome.required = isRequired;
                    afinidadeSelectHome.required = isRequired;
                    if (!isRequired) {
                        vinculoInputHome.value = '';
                    }
                }
                
                toggleInscricaoFieldsHome(); // Executa ao carregar
                exerceAtividadeSelectHome.addEventListener('change', toggleInscricaoFieldsHome);
            }
        });
    </script>
</body>
</html>
