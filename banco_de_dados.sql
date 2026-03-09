-- MySQL Script Otimizado para Análise de Dados e Controle Acadêmico
-- Banco de Dados: forma_muana
-- Engine: InnoDB

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema forma_muana
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `forma_muana` ;
CREATE SCHEMA IF NOT EXISTS `forma_muana` DEFAULT CHARACTER SET utf8 ;
USE `forma_muana` ;

-- -----------------------------------------------------
-- Table `forma_muana`.`tipo_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`tipo_usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`usuarios` (Sistema Geral: Admins, Professores)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  `login` VARCHAR(45) NULL,
  `senha` VARCHAR(255) NULL, -- Expandido para suportar hashes de senha
  `email` VARCHAR(100) NULL,
  `telefone` VARCHAR(20) NULL,
  `tipo_usuario_id` INT NOT NULL,
  `data_cadastro` DATETIME DEFAULT CURRENT_TIMESTAMP, -- Útil para métricas de adoção
  `termo_aceite` VARCHAR(45) NULL,
  `upload_rg` VARCHAR(255) NULL,
  `upload_cpf` VARCHAR(255) NULL,
  `upload_plano` VARCHAR(255) NULL,
  `upload_pis_ou_nis` VARCHAR(255) NULL,
  `upload_residencia` VARCHAR(255) NULL,
  `upload_cart_trab` VARCHAR(255) NULL,
  `upload_declaracao_vinculo_matr` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_usuarios_tipo_usuario_idx` (`tipo_usuario_id` ASC),
  CONSTRAINT `fk_usuarios_tipo_usuario`
    FOREIGN KEY (`tipo_usuario_id`)
    REFERENCES `forma_muana`.`tipo_usuario` (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`oferta` (Cursos/Turmas)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`oferta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  `descricao` TEXT NULL,
  `eixo_tecnologico` VARCHAR(100) NULL, -- Ex: Informática, Gestão, Artes
  `foto` VARCHAR(255) NULL,
  `inicio_insc` DATE NULL,
  `final_insc` DATE NULL,
  `status_oferta` VARCHAR(45) NULL, -- Ex: Em Inscrição, Em Andamento, Concluída
  `usuarios_id` INT NOT NULL, -- Usuário responsável pelo cadastro
  `local` VARCHAR(150) NULL,
  `turno` VARCHAR(45) NULL,
  `numero_vagas` INT NULL,
  `publico_alvo` VARCHAR(100) NULL,
  `modalidade` VARCHAR(45) NULL, -- Ex: Presencial, EAD, Híbrido
  `carga_horaria` INT NULL,
  `inicio_aulas` DATE NULL,
  `fim_aulas` DATE NULL,
  `inicio_hora_aula` TIME NULL,
  `fim_hora_aula` TIME NULL,
  `upload_relatorio` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_oferta_usuarios1_idx` (`usuarios_id` ASC),
  CONSTRAINT `fk_oferta_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `forma_muana`.`usuarios` (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`alunos` (Dados Biográficos e Inclusão)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`alunos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NULL,
  `whatsapp` VARCHAR(20) NULL,
  `telefone` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `data_nascimento` DATE NULL, -- Melhor que faixa etária para ciência de dados
  `sexo` VARCHAR(20) NULL,
  `identidade_genero` VARCHAR(45) NULL, -- Adição para análises sociológicas
  `possui_nome_social` VARCHAR(5) NULL,
  `nome_social` VARCHAR(100) NULL,
  `possui_deficiencia` VARCHAR(5) NULL,
  `tipo_deficiencia` VARCHAR(100) NULL,
  `cor_raca` VARCHAR(45) NULL,
  `comunidade_tradicional` VARCHAR(100) NULL, -- Ex: Quilombola, Indígena, Ribeirinho
  `zona_residencia` VARCHAR(20) NULL,
  `cidade_origem` VARCHAR(100) NULL,
  `cidade_atual` VARCHAR(100) NULL,
  `bairro_comunidade` VARCHAR(100) NULL,
  `estado_civil` VARCHAR(45) NULL,
  `possui_filhos` VARCHAR(5) NULL,
  `quantidade_filhos` INT NULL,
  `tipo_escola_ensino_medio` VARCHAR(100) NULL,
  `nivel_escolaridade` VARCHAR(100) NULL,
  `beneficiario_programa_social` VARCHAR(100) NULL, -- Ex: Bolsa Família
  `acesso_internet` VARCHAR(100) NULL, -- Ex: Banda Larga, Dados Móveis Limitados, Não possui
  `dispositivo_principal` VARCHAR(100) NULL, -- Ex: Celular próprio, PC compartilhado
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`alunos_has_oferta` (Inscrição e Desempenho)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`alunos_has_oferta` (
  `alunos_id` INT NOT NULL,
  `oferta_id` INT NOT NULL,
  `status_selecao` VARCHAR(45) NULL, -- Ex: Inscrito, Aprovado, Evadido, Concluído
  `data_insc` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `termo_aceite` TEXT NULL,
  `exerce_atividade_remunerada` VARCHAR(5) NULL,
  `vinculo_atividade` VARCHAR(100) NULL,
  `afinidade_atividade_curso` VARCHAR(5) NULL,
  `participacao_economica_familiar` VARCHAR(100) NULL,
  `numero_membros_familia` INT NULL,
  `renda_familiar` VARCHAR(100) NULL,
  `motivo_escolha_curso` TEXT NULL,
  `o_que_mais_espera_do_curso` TEXT NULL,
  `expectativas_relacao_curso` TEXT NULL,
  `expectativas_profissionais_pos_curso` TEXT NULL,
  `canal_informacao_curso` VARCHAR(100) NULL,
  `turno_preferencia` VARCHAR(45) NULL,
  -- Variáveis de Análise Acadêmica
  `concluiu_curso` TINYINT(1) DEFAULT 0,
  `nota_final` DECIMAL(5,2) NULL,
  `motivo_evasao` VARCHAR(150) NULL, -- Crucial para criar políticas de retenção
  PRIMARY KEY (`alunos_id`, `oferta_id`),
  INDEX `fk_alunos_has_oferta_oferta1_idx` (`oferta_id` ASC),
  INDEX `fk_alunos_has_oferta_alunos1_idx` (`alunos_id` ASC),
  CONSTRAINT `fk_alunos_has_oferta_alunos1`
    FOREIGN KEY (`alunos_id`)
    REFERENCES `forma_muana`.`alunos` (`id`),
  CONSTRAINT `fk_alunos_has_oferta_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`professor` (Vínculo de Ensino)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`professor` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuarios_id` INT NOT NULL, -- Identifica a pessoa (prof) cadastrada
  `oferta_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_professor_usuarios1_idx` (`usuarios_id` ASC),
  INDEX `fk_professor_oferta1_idx` (`oferta_id` ASC),
  CONSTRAINT `fk_professor_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `forma_muana`.`usuarios` (`id`),
  CONSTRAINT `fk_professor_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`aula` (NOVA TABELA: O Plano de Aula)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`aula` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `oferta_id` INT NOT NULL,
  `data_aula` DATE NOT NULL,
  `conteudo_ministrado` TEXT NULL,
  `carga_horaria_aula` INT NULL, -- Ex: 2 horas, 4 horas
  PRIMARY KEY (`id`),
  INDEX `fk_aula_oferta1_idx` (`oferta_id` ASC),
  CONSTRAINT `fk_aula_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `forma_muana`.`lista_frequencia` (Ajustada para Boas Práticas)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`lista_frequencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `aula_id` INT NOT NULL,
  `alunos_id` INT NOT NULL,
  `presenca` TINYINT(1) NOT NULL DEFAULT 0, -- 1 para Presente, 0 para Faltou
  `justificativa_falta` VARCHAR(255) NULL, -- Ex: Atestado médico, Transporte
  PRIMARY KEY (`id`),
  INDEX `fk_lista_frequencia_aula1_idx` (`aula_id` ASC),
  INDEX `fk_lista_frequencia_alunos1_idx` (`alunos_id` ASC),
  CONSTRAINT `fk_lista_frequencia_aula1`
    FOREIGN KEY (`aula_id`)
    REFERENCES `forma_muana`.`aula` (`id`),
  CONSTRAINT `fk_lista_frequencia_alunos1`
    FOREIGN KEY (`alunos_id`)
    REFERENCES `forma_muana`.`alunos` (`id`))
ENGINE = InnoDB;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;