<?php
// Define valores padrão para evitar erros de undefined index
$aluno = $aluno ?? [];
$formAction = $formAction ?? 'index.php?action=register_aluno';
$btnText = isset($isEditing) && $isEditing ? 'Salvar Alterações' : 'Concluir Cadastro';
?>
<div class="card" style="padding: 30px; background: #fff; border-radius: 10px; box-shadow: var(--shadow);">
<h2 style="margin-bottom: 20px;"><?= isset($isEditing) && $isEditing ? 'Editar Dados' : 'Cadastro do Aluno' ?></h2>
<form action="<?= htmlspecialchars($formAction) ?>" method="POST">
    <fieldset>
        <legend>Dados Pessoais</legend>
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="whatsapp">WhatsApp</label>
            <input type="text" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($aluno['whatsapp'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="telefone">Outro Telefone (Opcional)</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($aluno['telefone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($aluno['data_nascimento'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="estado_civil">Estado Civil</label>
            <select id="estado_civil" name="estado_civil" required>
                <option value="">Selecione...</option>
                <option value="Solteiro(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Solteiro(a)') ? 'selected' : '' ?>>Solteiro(a)</option>
                <option value="Casado(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Casado(a)') ? 'selected' : '' ?>>Casado(a)</option>
                <option value="Divorciado(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Divorciado(a)') ? 'selected' : '' ?>>Divorciado(a)</option>
                <option value="Viúvo(a)" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Viúvo(a)') ? 'selected' : '' ?>>Viúvo(a)</option>
                <option value="União Estável" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'União Estável') ? 'selected' : '' ?>>União Estável</option>
                <option value="Não informado" <?= (isset($aluno['estado_civil']) && $aluno['estado_civil'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option>
            </select>
        </div>
    </fieldset>

    <fieldset>
        <legend>Identidade e Gênero</legend>
        <div class="form-group">
            <label for="sexo">Sexo</label>
            <select id="sexo" name="sexo" required>
                <option value="">Selecione...</option>
                <option value="Masculino" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Masculino') ? 'selected' : '' ?>>Masculino</option>
                <option value="Feminino" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Feminino') ? 'selected' : '' ?>>Feminino</option>
                <option value="Não declarado" <?= (isset($aluno['sexo']) && $aluno['sexo'] == 'Não declarado') ? 'selected' : '' ?>>Não declarado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="identidade_genero">Identidade de Gênero</label>
            <select id="identidade_genero" name="identidade_genero" required>
                <option value="">Selecione...</option>
                <option value="Homem Cis" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Homem Cis') ? 'selected' : '' ?>>Homem Cis</option>
                <option value="Mulher Cis" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Mulher Cis') ? 'selected' : '' ?>>Mulher Cis</option>
                <option value="Homem Trans" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Homem Trans') ? 'selected' : '' ?>>Homem Trans</option>
                <option value="Mulher Trans" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Mulher Trans') ? 'selected' : '' ?>>Mulher Trans</option>
                <option value="Não-binário" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Não-binário') ? 'selected' : '' ?>>Não-binário</option>
                <option value="Outro" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                <option value="Não informado" <?= (isset($aluno['identidade_genero']) && $aluno['identidade_genero'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="possui_nome_social">Possui Nome Social?</label>
            <select id="possui_nome_social" name="possui_nome_social" required>
                <option value="Nao" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group" id="nome_social_group">
            <label for="nome_social">Nome Social</label>
            <input type="text" id="nome_social" name="nome_social" value="<?= htmlspecialchars($aluno['nome_social'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Questões Sociais e Étnicas</legend>
        <div class="form-group">
            <label for="cor_raca">Cor/Raça</label>
            <select id="cor_raca" name="cor_raca" required>
                <option value="">Selecione...</option>
                <option value="Branca" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Branca') ? 'selected' : '' ?>>Branca</option>
                <option value="Preta" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Preta') ? 'selected' : '' ?>>Preta</option>
                <option value="Parda" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Parda') ? 'selected' : '' ?>>Parda</option>
                <option value="Amarela" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Amarela') ? 'selected' : '' ?>>Amarela</option>
                <option value="Indígena" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Indígena') ? 'selected' : '' ?>>Indígena</option>
                <option value="Não declarado" <?= (isset($aluno['cor_raca']) && $aluno['cor_raca'] == 'Não declarado') ? 'selected' : '' ?>>Não declarado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="possui_deficiencia">Possui Deficiência?</label>
            <select id="possui_deficiencia" name="possui_deficiencia" required>
                <option value="Nao" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group" id="tipo_deficiencia_group">
            <label for="tipo_deficiencia">Tipo da Deficiência</label>
            <input type="text" id="tipo_deficiencia" name="tipo_deficiencia" value="<?= htmlspecialchars($aluno['tipo_deficiencia'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="comunidade_tradicional">Pertence a Comunidade Tradicional?</label>
            <select id="comunidade_tradicional" name="comunidade_tradicional" required>
                <option value="">Selecione...</option>
                <option value="Nenhuma" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Nenhuma') ? 'selected' : '' ?>>Nenhuma</option>
                <option value="Quilombola" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Quilombola') ? 'selected' : '' ?>>Quilombola</option>
                <option value="Indígena" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Indígena') ? 'selected' : '' ?>>Indígena</option>
                <option value="Ribeirinha" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Ribeirinha') ? 'selected' : '' ?>>Ribeirinha</option>
                <option value="Outra" <?= (isset($aluno['comunidade_tradicional']) && $aluno['comunidade_tradicional'] == 'Outra') ? 'selected' : '' ?>>Outra</option>
            </select>
        </div>
    </fieldset>

    <fieldset>
        <legend>Residência</legend>
        <div class="form-group">
            <label for="zona_residencia">Zona de Residência</label>
            <select id="zona_residencia" name="zona_residencia" required>
                <option value="">Selecione...</option>
                <option value="Urbana" <?= (isset($aluno['zona_residencia']) && $aluno['zona_residencia'] == 'Urbana') ? 'selected' : '' ?>>Urbana</option>
                <option value="Rural" <?= (isset($aluno['zona_residencia']) && $aluno['zona_residencia'] == 'Rural') ? 'selected' : '' ?>>Rural</option>
            </select>
        </div>
        <div class="form-group">
            <label for="cidade_origem">Cidade de Origem</label>
            <input type="text" id="cidade_origem" name="cidade_origem" value="<?= htmlspecialchars($aluno['cidade_origem'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="cidade_atual">Cidade Atual</label>
            <input type="text" id="cidade_atual" name="cidade_atual" value="<?= htmlspecialchars($aluno['cidade_atual'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="bairro_comunidade">Bairro/Comunidade</label>
            <input type="text" id="bairro_comunidade" name="bairro_comunidade" value="<?= htmlspecialchars($aluno['bairro_comunidade'] ?? '') ?>" required>
        </div>
    </fieldset>

    <fieldset>
        <legend>Família e Escolaridade</legend>
        <div class="form-group">
            <label for="possui_filhos">Possui Filhos?</label>
            <select id="possui_filhos" name="possui_filhos" required>
                <option value="Nao" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group" id="quantidade_filhos_group">
            <label for="quantidade_filhos">Quantidade de Filhos</label>
            <input type="number" id="quantidade_filhos" name="quantidade_filhos" min="0" value="<?= htmlspecialchars($aluno['quantidade_filhos'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="nivel_escolaridade">Nível de Escolaridade</label>
            <select id="nivel_escolaridade" name="nivel_escolaridade" required>
                <option value="">Selecione...</option>
                <option value="Ensino Fundamental Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Fundamental Incompleto') ? 'selected' : '' ?>>Ensino Fundamental Incompleto</option>
                <option value="Ensino Fundamental Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Fundamental Completo') ? 'selected' : '' ?>>Ensino Fundamental Completo</option>
                <option value="Ensino Médio Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Médio Incompleto') ? 'selected' : '' ?>>Ensino Médio Incompleto</option>
                <option value="Ensino Médio Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Médio Completo') ? 'selected' : '' ?>>Ensino Médio Completo</option>
                <option value="Ensino Superior Incompleto" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Superior Incompleto') ? 'selected' : '' ?>>Ensino Superior Incompleto</option>
                <option value="Ensino Superior Completo" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Ensino Superior Completo') ? 'selected' : '' ?>>Ensino Superior Completo</option>
                <option value="Pós-graduação" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Pós-graduação') ? 'selected' : '' ?>>Pós-graduação</option>
                <option value="Não informado" <?= (isset($aluno['nivel_escolaridade']) && $aluno['nivel_escolaridade'] == 'Não informado') ? 'selected' : '' ?>>Não informado</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_escola_ensino_medio">Tipo de Escola no Ensino Médio</label>
            <select id="tipo_escola_ensino_medio" name="tipo_escola_ensino_medio" required>
                <option value="">Selecione...</option>
                <option value="Pública" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Pública') ? 'selected' : '' ?>>Pública</option>
                <option value="Particular" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Particular') ? 'selected' : '' ?>>Particular</option>
                <option value="Bolsista em escola particular" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Bolsista em escola particular') ? 'selected' : '' ?>>Bolsista em escola particular</option>
                <option value="Outro" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
                <option value="Não se aplica" <?= (isset($aluno['tipo_escola_ensino_medio']) && $aluno['tipo_escola_ensino_medio'] == 'Não se aplica') ? 'selected' : '' ?>>Não se aplica</option>
            </select>
        </div>
    </fieldset>

    <fieldset>
        <legend>Informações Socioeconômicas</legend>
        <div class="form-group">
            <label for="beneficiario_programa_social">É beneficiário de programa social?</label>
            <select id="beneficiario_programa_social" name="beneficiario_programa_social" required>
                <option value="Nao" <?= (isset($aluno['beneficiario_programa_social']) && $aluno['beneficiario_programa_social'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['beneficiario_programa_social']) && $aluno['beneficiario_programa_social'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group" id="nome_programa_social_group">
            <label for="nome_programa_social">Qual programa?</label>
            <input type="text" id="nome_programa_social" name="nome_programa_social" value="<?= htmlspecialchars($aluno['nome_programa_social'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="acesso_internet">Tipo de Acesso à Internet</label>
            <select id="acesso_internet" name="acesso_internet" required>
                <option value="">Selecione...</option>
                <option value="Banda Larga Residencial" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Banda Larga Residencial') ? 'selected' : '' ?>>Banda Larga Residencial</option>
                <option value="Dados Móveis" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Dados Móveis') ? 'selected' : '' ?>>Dados Móveis</option>
                <option value="Internet via Rádio" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Internet via Rádio') ? 'selected' : '' ?>>Internet via Rádio</option>
                <option value="Não possui acesso" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Não possui acesso') ? 'selected' : '' ?>>Não possui acesso</option>
                <option value="Outro" <?= (isset($aluno['acesso_internet']) && $aluno['acesso_internet'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="dispositivo_principal">Dispositivo Principal de Acesso</label>
            <select id="dispositivo_principal" name="dispositivo_principal" required>
                <option value="">Selecione...</option>
                <option value="Celular" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Celular') ? 'selected' : '' ?>>Celular</option>
                <option value="Computador (Desktop)" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Computador (Desktop)') ? 'selected' : '' ?>>Computador (Desktop)</option>
                <option value="Notebook" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Notebook') ? 'selected' : '' ?>>Notebook</option>
                <option value="Tablet" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Tablet') ? 'selected' : '' ?>>Tablet</option>
                <option value="Outro" <?= (isset($aluno['dispositivo_principal']) && $aluno['dispositivo_principal'] == 'Outro') ? 'selected' : '' ?>>Outro</option>
            </select>
        </div>
    </fieldset>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem;"><?= $btnText ?></button>
</form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper function to toggle visibility
    function toggleVisibility(selectElement, targetGroup, showValue, makeRequired = true) {
        if (!selectElement || !targetGroup) return;
        const input = targetGroup.querySelector('input, select, textarea');

        function checkVisibility() {
            const isVisible = selectElement.value === showValue;
            targetGroup.style.display = isVisible ? '' : 'none';
            if (input && makeRequired) {
                input.required = isVisible;
            }
        }
        checkVisibility();
        selectElement.addEventListener('change', checkVisibility);
    }

    // Conditional visibility for social name
    var possuiNomeSocialSelect = document.getElementById('possui_nome_social');
    var nomeSocialGroup = document.getElementById('nome_social_group');
    toggleVisibility(possuiNomeSocialSelect, nomeSocialGroup, 'Sim', false); // Nome Social é opcional

    // Conditional visibility for disability type
    var possuiDeficienciaSelect = document.getElementById('possui_deficiencia');
    var tipoDeficienciaGroup = document.getElementById('tipo_deficiencia_group');
    toggleVisibility(possuiDeficienciaSelect, tipoDeficienciaGroup, 'Sim');
    
    // Conditional visibility for children quantity
    var possuiFilhosSelect = document.getElementById('possui_filhos');
    var quantidadeFilhosGroup = document.getElementById('quantidade_filhos_group');
    toggleVisibility(possuiFilhosSelect, quantidadeFilhosGroup, 'Sim');

    // Conditional visibility for social program
    var beneficiarioSelect = document.getElementById('beneficiario_programa_social');
    var nomeProgramaGroup = document.getElementById('nome_programa_social_group');
    toggleVisibility(beneficiarioSelect, nomeProgramaGroup, 'Sim');

    // Adiciona lógica para zerar a quantidade de filhos
    if (possuiFilhosSelect) {
        const quantidadeFilhosInput = document.getElementById('quantidade_filhos');

        const setFilhosValue = () => {
            if (possuiFilhosSelect.value === 'Nao') {
                quantidadeFilhosInput.value = 0;
            }
        };

        possuiFilhosSelect.addEventListener('change', setFilhosValue);
        setFilhosValue(); // Executa ao carregar a página
    }
});
</script>
