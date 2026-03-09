-- =========================================================================
-- Script SQL Unificado e Otimizado - Banco de Dados: forma_muana
-- Consolidado a partir dos scripts base, alterações e dump oficial.
-- Engine: InnoDB | Charset: utf8mb4
-- =========================================================================

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema forma_muana
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `forma_muana` ;
CREATE SCHEMA IF NOT EXISTS `forma_muana` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
USE `forma_muana` ;

-- -----------------------------------------------------
-- Table `forma_muana`.`tipo_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`tipo_usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`usuarios` (Sistema Geral: Admins, Professores, Alunos logados)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  `login` VARCHAR(45) NULL,
  `senha` VARCHAR(255) NULL, 
  `email` VARCHAR(100) NULL,
  `telefone` VARCHAR(20) NULL,
  `tipo_usuario_id` INT NOT NULL,
  `data_cadastro` DATETIME DEFAULT CURRENT_TIMESTAMP, 
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
    REFERENCES `forma_muana`.`tipo_usuario` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`oferta` (Cursos/Turmas)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`oferta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  `descricao` TEXT NULL,
  `eixo_tecnologico` VARCHAR(100) NULL,
  `foto` VARCHAR(255) NULL,
  `inicio_insc` DATE NULL,
  `final_insc` DATE NULL,
  `status_oferta` VARCHAR(45) NULL,
  `usuarios_id` INT NOT NULL,
  `local` VARCHAR(150) NULL,
  `turno` VARCHAR(45) NULL,
  `numero_vagas` INT NULL,
  `publico_alvo` VARCHAR(100) NULL,
  `modalidade` VARCHAR(45) NULL,
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
    REFERENCES `forma_muana`.`usuarios` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`alunos` (Dados Biográficos e Inclusão)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`alunos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NULL,
  `whatsapp` VARCHAR(20) NULL,
  `telefone` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `data_nascimento` DATE NULL,
  `sexo` VARCHAR(20) NULL,
  `identidade_genero` VARCHAR(45) NULL,
  `possui_nome_social` VARCHAR(5) NULL,
  `nome_social` VARCHAR(100) NULL,
  `possui_deficiencia` VARCHAR(5) NULL,
  `tipo_deficiencia` VARCHAR(100) NULL,
  `cor_raca` VARCHAR(45) NULL,
  `comunidade_tradicional` VARCHAR(100) NULL,
  `zona_residencia` VARCHAR(20) NULL,
  `cidade_origem` VARCHAR(100) NULL,
  `cidade_atual` VARCHAR(100) NULL,
  `bairro_comunidade` VARCHAR(100) NULL,
  `estado_civil` VARCHAR(45) NULL,
  `possui_filhos` VARCHAR(5) NULL,
  `quantidade_filhos` INT NULL,
  `tipo_escola_ensino_medio` VARCHAR(100) NULL,
  `nivel_escolaridade` VARCHAR(100) NULL,
  `beneficiario_programa_social` VARCHAR(100) NULL,
  `acesso_internet` VARCHAR(100) NULL,
  `dispositivo_principal` VARCHAR(100) NULL,
  `usuarios_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_fk_alunos_usuarios` (`usuarios_id` ASC),
  CONSTRAINT `fk_alunos_usuarios`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `forma_muana`.`usuarios` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`alunos_has_oferta` (Inscrição e Desempenho)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`alunos_has_oferta` (
  `alunos_id` INT NOT NULL,
  `oferta_id` INT NOT NULL,
  `status_selecao` VARCHAR(45) NULL,
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
  `concluiu_curso` TINYINT(1) DEFAULT 0,
  `nota_final` DECIMAL(5,2) NULL,
  `motivo_evasao` VARCHAR(150) NULL,
  PRIMARY KEY (`alunos_id`, `oferta_id`),
  INDEX `fk_alunos_has_oferta_oferta1_idx` (`oferta_id` ASC),
  INDEX `fk_alunos_has_oferta_alunos1_idx` (`alunos_id` ASC),
  CONSTRAINT `fk_alunos_has_oferta_alunos1`
    FOREIGN KEY (`alunos_id`)
    REFERENCES `forma_muana`.`alunos` (`id`),
  CONSTRAINT `fk_alunos_has_oferta_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`professor` (Vínculo de Ensino)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`professor` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuarios_id` INT NOT NULL,
  `oferta_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_professor_usuarios1_idx` (`usuarios_id` ASC),
  INDEX `fk_professor_oferta1_idx` (`oferta_id` ASC),
  CONSTRAINT `fk_professor_usuarios1`
    FOREIGN KEY (`usuarios_id`)
    REFERENCES `forma_muana`.`usuarios` (`id`),
  CONSTRAINT `fk_professor_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`aula` (Plano de Aula)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`aula` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `oferta_id` INT NOT NULL,
  `data_aula` DATE NOT NULL,
  `conteudo_ministrado` TEXT NULL,
  `carga_horaria_aula` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_aula_oferta1_idx` (`oferta_id` ASC),
  CONSTRAINT `fk_aula_oferta1`
    FOREIGN KEY (`oferta_id`)
    REFERENCES `forma_muana`.`oferta` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------------------------------------
-- Table `forma_muana`.`lista_frequencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forma_muana`.`lista_frequencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `aula_id` INT NOT NULL,
  `alunos_id` INT NOT NULL,
  `presenca` TINYINT(1) NOT NULL DEFAULT 0, 
  `justificativa_falta` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_lista_frequencia_aula1_idx` (`aula_id` ASC),
  INDEX `fk_lista_frequencia_alunos1_idx` (`alunos_id` ASC),
  CONSTRAINT `fk_lista_frequencia_aula1`
    FOREIGN KEY (`aula_id`)
    REFERENCES `forma_muana`.`aula` (`id`),
  CONSTRAINT `fk_lista_frequencia_alunos1`
    FOREIGN KEY (`alunos_id`)
    REFERENCES `forma_muana`.`alunos` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO `usuarios` (`id`, `nome`, `login`, `senha`, `email`, `telefone`, `tipo_usuario_id`, 
`data_cadastro`, `termo_aceite`, `upload_rg`, `upload_cpf`, `upload_plano`, `upload_pis_ou_nis`, 
`upload_residencia`, `upload_cart_trab`, `upload_declaracao_vinculo_matr`) VALUES 
(NULL, 'fabricio', 'fabricio', '$2y$10$TON4RWkPz7LdEQezMDKZQOJ/lsWAsciI/k91JJndIUTwq4YG5tC.m', 
'fabriciosf@ufpa.br', NULL, '1', CURRENT_TIMESTAMP, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);