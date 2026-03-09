-- Passo 1: Adicionar a coluna para a chave estrangeira na tabela 'alunos'.
-- Esta coluna irá armazenar o ID correspondente da tabela 'usuarios'.
-- Começamos permitindo que ela seja nula para não dar erro em registros existentes.
ALTER TABLE `alunos`
ADD COLUMN `usuarios_id` INT NULL DEFAULT NULL AFTER `dispositivo_principal`,
ADD INDEX `idx_fk_alunos_usuarios` (`usuarios_id` ASC);


-- Passo 2: Preencher a nova coluna 'usuarios_id' com base nos emails correspondentes.
-- Este comando atualiza os registros de alunos existentes, preenchendo a nova coluna
-- com o ID da tabela 'usuarios' onde os emails são iguais.
UPDATE `alunos` a
JOIN `usuarios` u ON a.email = u.email
SET a.usuarios_id = u.id
WHERE a.usuarios_id IS NULL;


-- Passo 3: Adicionar a restrição de chave estrangeira (Foreign Key).
-- Isso cria a relação formal entre as tabelas, garantindo a integridade dos dados.
ALTER TABLE `alunos`
ADD CONSTRAINT `fk_alunos_usuarios`
  FOREIGN KEY (`usuarios_id`)
  REFERENCES `usuarios` (`id`)
  ON DELETE SET NULL  -- Se um usuário for deletado, o vínculo no aluno se torna nulo.
  ON UPDATE CASCADE;  -- Se o ID do usuário for alterado, o vínculo no aluno é atualizado.

-- Passo 4: Adicionar campos de data e hora das aulas na tabela 'oferta'.
ALTER TABLE `oferta`
ADD COLUMN `inicio_aulas` DATE NULL DEFAULT NULL AFTER `carga_horaria`,
ADD COLUMN `fim_aulas` DATE NULL DEFAULT NULL AFTER `inicio_aulas`,
ADD COLUMN `inicio_hora_aula` TIME NULL DEFAULT NULL AFTER `fim_aulas`,
ADD COLUMN `fim_hora_aula` TIME NULL DEFAULT NULL AFTER `inicio_hora_aula`;
