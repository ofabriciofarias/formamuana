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
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($aluno['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="whatsapp">WhatsApp</label>
            <input type="text" id="whatsapp" name="whatsapp" value="<?= htmlspecialchars($aluno['whatsapp'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="telefone">Outro Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($aluno['telefone'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="data_nascimento">Data de Nascimento</label>
            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($aluno['data_nascimento'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="estado_civil">Estado Civil</label>
            <input type="text" id="estado_civil" name="estado_civil" value="<?= htmlspecialchars($aluno['estado_civil'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Identidade e Gênero</legend>
        <div class="form-group">
            <label for="sexo">Sexo</label>
            <input type="text" id="sexo" name="sexo" value="<?= htmlspecialchars($aluno['sexo'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="identidade_genero">Identidade de Gênero</label>
            <input type="text" id="identidade_genero" name="identidade_genero" value="<?= htmlspecialchars($aluno['identidade_genero'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="possui_nome_social">Possui Nome Social?</label>
            <select id="possui_nome_social" name="possui_nome_social">
                <option value="Nao" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_nome_social']) && $aluno['possui_nome_social'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group">
            <label for="nome_social">Nome Social</label>
            <input type="text" id="nome_social" name="nome_social" value="<?= htmlspecialchars($aluno['nome_social'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Questões Sociais e Étnicas</legend>
        <div class="form-group">
            <label for="cor_raca">Cor/Raça</label>
            <input type="text" id="cor_raca" name="cor_raca" value="<?= htmlspecialchars($aluno['cor_raca'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="possui_deficiencia">Possui Deficiência?</label>
            <select id="possui_deficiencia" name="possui_deficiencia">
                <option value="Nao" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_deficiencia']) && $aluno['possui_deficiencia'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_deficiencia">Tipo da Deficiência</label>
            <input type="text" id="tipo_deficiencia" name="tipo_deficiencia" value="<?= htmlspecialchars($aluno['tipo_deficiencia'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="comunidade_tradicional">Pertence a Comunidade Tradicional?</label>
            <input type="text" id="comunidade_tradicional" name="comunidade_tradicional" placeholder="Ex: Quilombola, Indígena, etc." value="<?= htmlspecialchars($aluno['comunidade_tradicional'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Residência</legend>
        <div class="form-group">
            <label for="zona_residencia">Zona de Residência</label>
            <input type="text" id="zona_residencia" name="zona_residencia" placeholder="Urbana ou Rural" value="<?= htmlspecialchars($aluno['zona_residencia'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="cidade_origem">Cidade de Origem</label>
            <input type="text" id="cidade_origem" name="cidade_origem" value="<?= htmlspecialchars($aluno['cidade_origem'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="cidade_atual">Cidade Atual</label>
            <input type="text" id="cidade_atual" name="cidade_atual" value="<?= htmlspecialchars($aluno['cidade_atual'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="bairro_comunidade">Bairro/Comunidade</label>
            <input type="text" id="bairro_comunidade" name="bairro_comunidade" value="<?= htmlspecialchars($aluno['bairro_comunidade'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Família e Escolaridade</legend>
        <div class="form-group">
            <label for="possui_filhos">Possui Filhos?</label>
            <select id="possui_filhos" name="possui_filhos">
                <option value="Nao" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Nao') ? 'selected' : '' ?>>Não</option>
                <option value="Sim" <?= (isset($aluno['possui_filhos']) && $aluno['possui_filhos'] == 'Sim') ? 'selected' : '' ?>>Sim</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantidade_filhos">Quantidade de Filhos</label>
            <input type="number" id="quantidade_filhos" name="quantidade_filhos" min="0" value="<?= htmlspecialchars($aluno['quantidade_filhos'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="nivel_escolaridade">Nível de Escolaridade</label>
            <input type="text" id="nivel_escolaridade" name="nivel_escolaridade" value="<?= htmlspecialchars($aluno['nivel_escolaridade'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="tipo_escola_ensino_medio">Tipo de Escola no Ensino Médio</label>
            <input type="text" id="tipo_escola_ensino_medio" name="tipo_escola_ensino_medio" placeholder="Pública, Particular, etc." value="<?= htmlspecialchars($aluno['tipo_escola_ensino_medio'] ?? '') ?>">
        </div>
    </fieldset>

    <fieldset>
        <legend>Informações Socioeconômicas</legend>
        <div class="form-group">
            <label for="beneficiario_programa_social">É beneficiário de programa social?</label>
            <input type="text" id="beneficiario_programa_social" name="beneficiario_programa_social" placeholder="Ex: Bolsa Família" value="<?= htmlspecialchars($aluno['beneficiario_programa_social'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="acesso_internet">Tipo de Acesso à Internet</label>
            <input type="text" id="acesso_internet" name="acesso_internet" placeholder="Banda Larga, Dados Móveis, etc." value="<?= htmlspecialchars($aluno['acesso_internet'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="dispositivo_principal">Dispositivo Principal de Acesso</label>
            <input type="text" id="dispositivo_principal" name="dispositivo_principal" placeholder="Celular, Computador, etc." value="<?= htmlspecialchars($aluno['dispositivo_principal'] ?? '') ?>">
        </div>
    </fieldset>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2rem;"><?= $btnText ?></button>
</form>
</div>
