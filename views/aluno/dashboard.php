    <!-- Hero Slider (Simulando a Home) -->
    <section id = "inicio" class="hero-slider">
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
                                    $encerrado = ($oferta->getFinalInsc() < $hoje);
                                    $detalhes = [
                                        'nome' => $oferta->getNome(),
                                        'descricao' => $oferta->getDescricao(),
                                        'eixo_tecnologico' => $oferta->getEixoTecnologico(),
                                        'foto' => $oferta->getFoto(),
                                        'inicio_insc' => date('d/m/Y', strtotime($oferta->getInicioInsc())),
                                        'final_insc' => date('d/m/Y', strtotime($oferta->getFinalInsc())),
                                        'local' => $oferta->getLocal(),
                                        'turno' => $oferta->getTurno(),
                                        'numero_vagas' => $oferta->getNumeroVagas(),
                                        'publico_alvo' => $oferta->getPublicoAlvo(),
                                        'modalidade' => $oferta->getModalidade(),
                                        'carga_horaria' => $oferta->getCargaHoraria(),
                                        'inicio_aulas' => $oferta->getInicioAulas() ? date('d/m/Y', strtotime($oferta->getInicioAulas())) : null,
                                        'fim_aulas' => $oferta->getFimAulas() ? date('d/m/Y', strtotime($oferta->getFimAulas())) : null,
                                        'inicio_hora_aula' => $oferta->getInicioHoraAula() ? date('H:i', strtotime($oferta->getInicioHoraAula())) : null,
                                        'fim_hora_aula' => $oferta->getFimHoraAula() ? date('H:i', strtotime($oferta->getFimHoraAula())) : null,
                                    ];
                                ?>
                                <tr>
                                    <td data-label="Nome" style="font-weight: bold;"><?= htmlspecialchars($oferta->getNome()) ?></td>
                                    <td data-label="Descrição"><?= htmlspecialchars(substr($oferta->getDescricao(), 0, 50)) . (strlen($oferta->getDescricao()) > 50 ? '...' : '') ?></td>
                                    <td data-label="Local"><?= htmlspecialchars($oferta->getLocal()) ?></td>
                                    <td data-label="Vagas"><?= htmlspecialchars($oferta->getNumeroVagas()) ?></td>
                                    <td data-label="Carga Horária"><?= htmlspecialchars($oferta->getCargaHoraria()) ?>h</td>
                                    <td data-label="Público Alvo"><?= htmlspecialchars($oferta->getPublicoAlvo()) ?></td>
                                    <td data-label="Inscrições Até"><?= date('d/m/Y', strtotime($oferta->getFinalInsc())) ?></td>
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
                                                <?php if (isset($ofertasInscritasIds) && in_array($oferta->getId(), $ofertasInscritasIds)): ?>
                                                    <span style="color: var(--secondary-color); font-weight: bold;">Inscrito como Sucesso</span>
                                                <?php else: ?>
                                                    <button onclick="openInscricaoModal('<?= $oferta->getId() ?>', '<?= $oferta->getTurno() ?>')" class="btn btn-success" style="padding: 5px 15px; font-size: 0.8rem;">Inscrever</button>
                                                <?php endif; ?>
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


    <!-- Seção de Minhas Inscrições -->
    <section id="minhas_inscricoes" class="section-padding" style="background-color: var(--bg-light);">
        <div class="container">
            <h2 class="section-title">Minhas Inscrições</h2>
            
            <div class="alert-info" style="margin-bottom: 30px; text-align: center;">
                <p style="margin: 0;">Acompanhe o status das suas inscrições. As inscrições são gratuitas e você pode se inscrever em quantas vagas forem de seu interesse!</p>
            </div>

            <?php if (!empty($minhasInscricoes)): ?>
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
                                <th>Status da Seleção</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($minhasInscricoes as $inscricao): ?>
                                <?php
                                    // Preparar dados para o modal de detalhes
                                    $detalhesInscricao = [
                                        'nome' => $inscricao['nome'],
                                        'descricao' => $inscricao['descricao'],
                                        'eixo_tecnologico' => $inscricao['eixo_tecnologico'],
                                        'foto' => $inscricao['foto'],
                                        'inicio_insc' => date('d/m/Y', strtotime($inscricao['inicio_insc'])),
                                        'final_insc' => date('d/m/Y', strtotime($inscricao['final_insc'])),
                                        'local' => $inscricao['local'],
                                        'turno' => $inscricao['turno'],
                                        'numero_vagas' => $inscricao['numero_vagas'],
                                        'publico_alvo' => $inscricao['publico_alvo'],
                                        'modalidade' => $inscricao['modalidade'],
                                        'carga_horaria' => $inscricao['carga_horaria'],
                                        'inicio_aulas' => $inscricao['inicio_aulas'] ? date('d/m/Y', strtotime($inscricao['inicio_aulas'])) : null,
                                        'fim_aulas' => $inscricao['fim_aulas'] ? date('d/m/Y', strtotime($inscricao['fim_aulas'])) : null,
                                        'inicio_hora_aula' => $inscricao['inicio_hora_aula'] ? date('H:i', strtotime($inscricao['inicio_hora_aula'])) : null,
                                        'fim_hora_aula' => $inscricao['fim_hora_aula'] ? date('H:i', strtotime($inscricao['fim_hora_aula'])) : null,
                                    ];
                                ?>
                                <tr>
                                    <td data-label="Nome"><?= htmlspecialchars($inscricao['nome']) ?></td>
                                    <td data-label="Descrição"><?= htmlspecialchars(substr($inscricao['descricao'], 0, 100)) . '...' ?></td>
                                    <td data-label="Local"><?= htmlspecialchars($inscricao['local']) ?></td>
                                    <td data-label="Vagas"><?= htmlspecialchars($inscricao['numero_vagas']) ?></td>
                                    <td data-label="Carga Horária"><?= htmlspecialchars($inscricao['carga_horaria']) ?>h</td>
                                    <td data-label="Público Alvo"><?= htmlspecialchars($inscricao['publico_alvo']) ?></td>
                                    <td data-label="Inscrições Até"><?= htmlspecialchars(date('d/m/Y', strtotime($inscricao['final_insc']))) ?></td>
                                    <td data-label="Detalhes" style="text-align: center;">
                                        <button onclick='openDetalhesOfertaModal(<?= json_encode($detalhesInscricao, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)' class="btn-view-details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td data-label="Status da Seleção"><strong><?= htmlspecialchars(ucfirst($inscricao['status_selecao'])) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="background: #eaf6ff; border-left: 5px solid #63a3d4; padding: 20px; margin-top: 20px; text-align: center; border-radius: 5px;">
                    <p style="font-size: 1.1rem; margin:0;">Você ainda não se inscreveu em nenhuma vaga.</p>
                    <a href="#ofertas" class="btn btn-primary" style="margin-top: 15px;">Ver Vagas Disponíveis</a>
                </div>
            <?php endif; ?>
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
                    <h3>Execução do Projeot</h3>
                    <p>Universidade Federal do Pará</p>
                    Reotpr Prof. Dr. Gilmar Silva
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
                            <input type="text" id="vinculo_atividade_modal" name="vinculo_atividade" class="form-control"> <!-- required é gerenciado por JS -->
                        </div>
                        <div class="form-group">
                            <label for="afinidade_atividade_curso_modal">Sua atividade remunerada tem afinidade com a área do curso?</label>
                            <select id="afinidade_atividade_curso_modal" name="afinidade_atividade_curso" class="form-control"> <!-- required é gerenciado por JS -->
                                <option value="" disabled selected>Selecione</option>
                                <option value="Sim">Sim</option>
                                <option value="Nao">Não</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="participacao_economica_familiar_modal">Qual sua principal participação na vida econômica familiar?</label>
                            <select id="participacao_economica_familiar_modal" name="participacao_economica_familiar" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <option value="Responsavel_principal">Responsável principal pelo sustento da família</option>
                                <option value="Ajuda_despesas">Ajudo a pagar as despesas</option>
                                <option value="Nao_contribuo">Não contribuo com as despesas</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="numero_membros_familia_modal">Quantas pessoas, incluindo você, moram em sua residência?</label>
                            <select id="numero_membros_familia_modal" name="numero_membros_familia" required class="form-control">
                                <option value="" disabled selected>Selecione</option>
                                <?php for ($i = 1; $i <= 20; $i++) : ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="renda_familiar_modal">Qual a renda total da sua família (incluindo você)?</label>
                            <select id="renda_familiar_modal" name="renda_familiar" required class="form-control">
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
                        <div class="form-group"><label for="motivo_escolha_curso_modal">Por que você escolheu este curso? Quais habilidades espera desenvolver?</label><textarea id="motivo_escolha_curso_modal" name="motivo_escolha_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="o_que_mais_espera_do_curso_modal">O que você mais espera do curso?</label><textarea id="o_que_mais_espera_do_curso_modal" name="o_que_mais_espera_do_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_relacao_curso_modal">Quais suas expectativas em relação ao curso?</label><textarea id="expectativas_relacao_curso_modal" name="expectativas_relacao_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="expectativas_profissionais_pos_curso_modal">Quais suas expectativas profissionais após a conclusão do curso?</label><textarea id="expectativas_profissionais_pos_curso_modal" name="expectativas_profissionais_pos_curso" rows="4" required class="form-control"></textarea></div>
                        <div class="form-group"><label for="canal_informacao_curso_modal">Como você ficou sabendo deste curso?</label><select id="canal_informacao_curso_modal" name="canal_informacao_curso" required class="form-control"><option value="" disabled selected>Selecione</option><option value="Redes_sociais">Redes Sociais (Instagram, Facebook, etc.)</option><option value="Amigos_familiares">Indicação de amigos ou familiares</option><option value="Site_instituicao">Site da Instituição</option><option value="Busca_internet">Busca na Internet (Google, etc.)</option><option value="Radio_tv">Rádio ou TV</option><option value="Outro">Outro</option></select></div>
                        <div class="form-group"><label for="turno_preferencia_modal">Qual o seu turno de preferência para estudar?</label><input type="text" id="turno_preferencia_modal" name="turno_preferencia" class="form-control" readonly></div>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Termo de Aceite</legend>
                        <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" id="termo_aceite_insc_modal" name="termo_aceite" value="Sim" required style="width: auto;"/>
                            <label for="termo_aceite_insc_modal" style="margin-bottom: 0;">Li e concordo com os termos.</label>
                            <a href="#" onclick="event.preventDefault(); openModal('inscricaoTermsModal');" class="terms-link">(Saber mais)</a>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem; margin-top: 20px;">Finalizar Inscrição</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Termos de Inscrição -->
    <div id="inscricaoTermsModal" class="modal modal-on-top">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('inscricaoTermsModal')">&times;</span>
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
                <button onclick="closeModal('inscricaoTermsModal')" class="btn btn-primary">Fechar</button>
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

    <a href="https://wa.me/5591992220689" class="fab-whatsapp" target="_blank" title="Fale conosco no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    
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

        function openInscricaoModal(ofertaId, turno) {
            const modal = document.getElementById('inscricaoOfertaModal');
            if (!modal) return;
            
            const form = modal.querySelector('form');
            form.action = `index.php?action=do_inscricao_oferta&oferta_id=${ofertaId}`;
            
            const turnoSelect = document.getElementById('turno_preferencia_modal');
            if(turnoSelect) turnoSelect.value = turno;

            openModal('inscricaoOfertaModal');
        }

        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($openCompleteProfileModal) && $openCompleteProfileModal): ?>
                openModal('profileModal');
                
                const msgDiv = document.createElement('div');
                msgDiv.style = 'background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ffeeba;';
                msgDiv.innerHTML = '<i class="fas fa-info-circle"></i> Por favor, complete seu cadastro para acessar todas as funcionalidades.';
                const modalBody = document.querySelector('#profileModal .modal-body');
                if (modalBody) {
                    modalBody.insertBefore(msgDiv, modalBody.firstChild);
                }
            <?php endif; ?>

            // Script para campos condicionais do formulário de perfil
            function toggleVisibility(selectElement, targetGroup, showValue) {
                if (!selectElement || !targetGroup) return;
                
                const input = targetGroup.querySelector('input, select, textarea');

                function checkVisibility() {
                    const isVisible = selectElement.value === showValue;
                    targetGroup.style.display = isVisible ? '' : 'none';
                    if (input) {
                        input.required = isVisible;
                    }
                }
                checkVisibility();
                selectElement.addEventListener('change', checkVisibility);
            }

            toggleVisibility(document.getElementById('possui_nome_social'), document.getElementById('nome_social_group'), 'Sim');
            toggleVisibility(document.getElementById('possui_deficiencia'), document.getElementById('tipo_deficiencia_group'), 'Sim');
            toggleVisibility(document.getElementById('possui_filhos'), document.getElementById('quantidade_filhos_group'), 'Sim');
            toggleVisibility(document.getElementById('beneficiario_programa_social'), document.getElementById('nome_programa_social_group'), 'Sim');

            // Adiciona lógica para zerar a quantidade de filhos no modal de perfil
            const possuiFilhosSelect = document.getElementById('possui_filhos');
            if (possuiFilhosSelect) {
                const quantidadeFilhosInput = document.getElementById('quantidade_filhos');

                const setFilhosValue = () => {
                    if (possuiFilhosSelect.value === 'Nao') {
                        quantidadeFilhosInput.value = 0;
                    }
                };

                possuiFilhosSelect.addEventListener('change', setFilhosValue);
                setFilhosValue(); // Garante o valor correto no carregamento
            }

            // Lógica para campos condicionais do formulário de inscrição
            const exerceAtividadeSelect = document.getElementById('exerce_atividade_remunerada_modal');
            if (exerceAtividadeSelect) {
                const vinculoInput = document.getElementById('vinculo_atividade_modal');
                const afinidadeSelect = document.getElementById('afinidade_atividade_curso_modal');
                const vinculoGroup = vinculoInput.closest('.form-group');
                const afinidadeGroup = afinidadeSelect.closest('.form-group');

                function toggleInscricaoFields() {
                    const isRequired = exerceAtividadeSelect.value === 'Sim';
                    vinculoGroup.style.display = isRequired ? 'block' : 'none';
                    afinidadeGroup.style.display = isRequired ? 'block' : 'none';
                    vinculoInput.required = isRequired;
                    afinidadeSelect.required = isRequired;
                    if (!isRequired) {
                        vinculoInput.value = '';
                    }
                }
                
                toggleInscricaoFields(); // Executa ao carregar
                exerceAtividadeSelect.addEventListener('change', toggleInscricaoFields);
            }
        });       
    </script>