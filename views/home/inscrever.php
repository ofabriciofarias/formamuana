<?php
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
<div class="container" style="padding-top: 120px; padding-bottom: 60px;">
    <div class="section-title">
        <h2>Ficha de Inscrição</h2>
    </div>
    
    <div class="login-container" style="max-width: 800px; margin: 0 auto;">
        <p style="margin-bottom: 20px; color: #666;">Por favor, preencha os dados socioeconômicos abaixo para confirmar sua inscrição na oferta selecionada.</p>

        <form action="/aluno/inscricao?oferta_id=<?= htmlspecialchars($_GET['oferta_id']) ?>" method="POST">
            
            <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <legend style="padding: 0 10px; font-weight: bold; color: var(--primary-color);">Dados Socioeconômicos</legend>

                <div class="form-group">
                    <label>Exerce atividade remunerada?</label>
                    <select name="exerce_atividade_remunerada" class="form-control" required>
                        <option value="">Selecione...</option>
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="vinculo_atividade">Se sim, qual o vínculo?</label>
                    <input type="text" name="vinculo_atividade" id="vinculo_atividade" placeholder="Ex: Formal, Informal, Autônomo">
                </div>

                <div class="form-group">
                    <label>A atividade tem afinidade com o curso?</label>
                    <select name="afinidade_atividade_curso" class="form-control">
                        <option value="">Selecione...</option>
                        <option value="Sim">Sim</option>
                        <option value="Não">Não</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="participacao_economica_familiar">Participação na economia familiar:</label>
                    <select name="participacao_economica_familiar" id="participacao_economica_familiar" required>
                        <option value="">Selecione...</option>
                        <option value="Principal Responsável">Principal Responsável</option>
                        <option value="Contribui Parcialmente">Contribui Parcialmente</option>
                        <option value="Não Contribui">Não Contribui</option>
                        <option value="Dependente">Dependente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="numero_membros_familia">Número de membros na família:</label>
                    <input type="number" name="numero_membros_familia" id="numero_membros_familia" required min="1">
                </div>

                <div class="form-group">
                    <label for="renda_familiar">Renda Familiar Aproximada:</label>
                    <select name="renda_familiar" id="renda_familiar" required>
                        <option value="">Selecione...</option>
                        <option value="Até 1 Salário Mínimo">Até 1 Salário Mínimo</option>
                        <option value="De 1 a 2 Salários Mínimos">De 1 a 2 Salários Mínimos</option>
                        <option value="De 2 a 3 Salários Mínimos">De 2 a 3 Salários Mínimos</option>
                        <option value="Acima de 3 Salários Mínimos">Acima de 3 Salários Mínimos</option>
                    </select>
                </div>
            </fieldset>

            <fieldset style="border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <legend style="padding: 0 10px; font-weight: bold; color: var(--primary-color);">Sobre o Curso</legend>

                <div class="form-group">
                    <label for="motivo_escolha_curso">Motivo da escolha do curso:</label>
                    <textarea name="motivo_escolha_curso" id="motivo_escolha_curso" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="o_que_mais_espera_do_curso">O que mais espera do curso?</label>
                    <textarea name="o_que_mais_espera_do_curso" id="o_que_mais_espera_do_curso" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="expectativas_relacao_curso">Expectativas em relação ao curso:</label>
                    <textarea name="expectativas_relacao_curso" id="expectativas_relacao_curso" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="expectativas_profissionais_pos_curso">Expectativas profissionais pós-curso:</label>
                    <textarea name="expectativas_profissionais_pos_curso" id="expectativas_profissionais_pos_curso" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="canal_informacao_curso">Como ficou sabendo do curso?</label>
                    <select name="canal_informacao_curso" id="canal_informacao_curso">
                        <option value="">Selecione...</option>
                        <option value="Redes Sociais">Redes Sociais</option>
                        <option value="Amigos/Familiares">Amigos/Familiares</option>
                        <option value="Site da Instituição">Site da Instituição</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="turno_preferencia">Qual o seu turno de preferência para estudar?</label>
                    <input type="text" name="turno_preferencia" id="turno_preferencia" value="<?= htmlspecialchars($turno_oferta) ?>" class="form-control" readonly>
                </div>
            </fieldset>

            <div class="form-group">
                <input type="checkbox" id="termo_aceite" name="termo_aceite" value="aceito" required>
                <label for="termo_aceite" style="display:inline;">Declaro que as informações acima são verdadeiras.</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">Confirmar Inscrição</button>
        </form>
    </div>
</div>