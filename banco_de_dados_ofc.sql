-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 09/03/2026 às 17:13
-- Versão do servidor: 8.4.7
-- Versão do PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `forma_muana`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

DROP TABLE IF EXISTS `alunos`;
CREATE TABLE IF NOT EXISTS `alunos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) DEFAULT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `identidade_genero` varchar(45) DEFAULT NULL,
  `possui_nome_social` varchar(5) DEFAULT NULL,
  `nome_social` varchar(100) DEFAULT NULL,
  `possui_deficiencia` varchar(5) DEFAULT NULL,
  `tipo_deficiencia` varchar(100) DEFAULT NULL,
  `cor_raca` varchar(45) DEFAULT NULL,
  `comunidade_tradicional` varchar(100) DEFAULT NULL,
  `zona_residencia` varchar(20) DEFAULT NULL,
  `cidade_origem` varchar(100) DEFAULT NULL,
  `cidade_atual` varchar(100) DEFAULT NULL,
  `bairro_comunidade` varchar(100) DEFAULT NULL,
  `estado_civil` varchar(45) DEFAULT NULL,
  `possui_filhos` varchar(5) DEFAULT NULL,
  `quantidade_filhos` int DEFAULT NULL,
  `tipo_escola_ensino_medio` varchar(100) DEFAULT NULL,
  `nivel_escolaridade` varchar(100) DEFAULT NULL,
  `beneficiario_programa_social` varchar(100) DEFAULT NULL,
  `acesso_internet` varchar(100) DEFAULT NULL,
  `dispositivo_principal` varchar(100) DEFAULT NULL,
  `usuarios_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_alunos_usuarios` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos_has_oferta`
--

DROP TABLE IF EXISTS `alunos_has_oferta`;
CREATE TABLE IF NOT EXISTS `alunos_has_oferta` (
  `alunos_id` int NOT NULL,
  `oferta_id` int NOT NULL,
  `status_selecao` varchar(45) DEFAULT NULL,
  `data_insc` datetime DEFAULT CURRENT_TIMESTAMP,
  `termo_aceite` text,
  `exerce_atividade_remunerada` varchar(5) DEFAULT NULL,
  `vinculo_atividade` varchar(100) DEFAULT NULL,
  `afinidade_atividade_curso` varchar(5) DEFAULT NULL,
  `participacao_economica_familiar` varchar(100) DEFAULT NULL,
  `numero_membros_familia` int DEFAULT NULL,
  `renda_familiar` varchar(100) DEFAULT NULL,
  `motivo_escolha_curso` text,
  `o_que_mais_espera_do_curso` text,
  `expectativas_relacao_curso` text,
  `expectativas_profissionais_pos_curso` text,
  `canal_informacao_curso` varchar(100) DEFAULT NULL,
  `turno_preferencia` varchar(45) DEFAULT NULL,
  `concluiu_curso` tinyint(1) DEFAULT '0',
  `nota_final` decimal(5,2) DEFAULT NULL,
  `motivo_evasao` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`alunos_id`,`oferta_id`),
  KEY `fk_alunos_has_oferta_oferta1_idx` (`oferta_id`),
  KEY `fk_alunos_has_oferta_alunos1_idx` (`alunos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aula`
--

DROP TABLE IF EXISTS `aula`;
CREATE TABLE IF NOT EXISTS `aula` (
  `id` int NOT NULL AUTO_INCREMENT,
  `oferta_id` int NOT NULL,
  `data_aula` date NOT NULL,
  `conteudo_ministrado` text,
  `carga_horaria_aula` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_aula_oferta1_idx` (`oferta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lista_frequencia`
--

DROP TABLE IF EXISTS `lista_frequencia`;
CREATE TABLE IF NOT EXISTS `lista_frequencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `aula_id` int NOT NULL,
  `alunos_id` int NOT NULL,
  `presenca` tinyint(1) NOT NULL DEFAULT '0',
  `justificativa_falta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_lista_frequencia_aula1_idx` (`aula_id`),
  KEY `fk_lista_frequencia_alunos1_idx` (`alunos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `oferta`
--

DROP TABLE IF EXISTS `oferta`;
CREATE TABLE IF NOT EXISTS `oferta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text,
  `inicio_aulas` varchar(45) NOT NULL,
  `fim_aulas` varchar(45) NOT NULL,
  `inicio_hora_aula` varchar(45) NOT NULL,
  `fim_hora_aula` varchar(45) NOT NULL,
  `eixo_tecnologico` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `inicio_insc` date DEFAULT NULL,
  `final_insc` date DEFAULT NULL,
  `status_oferta` varchar(45) DEFAULT NULL,
  `usuarios_id` int NOT NULL,
  `local` varchar(150) DEFAULT NULL,
  `turno` varchar(45) DEFAULT NULL,
  `numero_vagas` int DEFAULT NULL,
  `publico_alvo` varchar(100) DEFAULT NULL,
  `modalidade` varchar(45) DEFAULT NULL,
  `carga_horaria` int DEFAULT NULL,
  `upload_relatorio` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_oferta_usuarios1_idx` (`usuarios_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professor`
--

DROP TABLE IF EXISTS `professor`;
CREATE TABLE IF NOT EXISTS `professor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuarios_id` int NOT NULL,
  `oferta_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_professor_usuarios1_idx` (`usuarios_id`),
  KEY `fk_professor_oferta1_idx` (`oferta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
CREATE TABLE IF NOT EXISTS `tipo_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `login` varchar(45) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `tipo_usuario_id` int NOT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `termo_aceite` varchar(45) DEFAULT NULL,
  `upload_rg` varchar(255) DEFAULT NULL,
  `upload_cpf` varchar(255) DEFAULT NULL,
  `upload_plano` varchar(255) DEFAULT NULL,
  `upload_pis_ou_nis` varchar(255) DEFAULT NULL,
  `upload_residencia` varchar(255) DEFAULT NULL,
  `upload_cart_trab` varchar(255) DEFAULT NULL,
  `upload_declaracao_vinculo_matr` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usuarios_tipo_usuario_idx` (`tipo_usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `fk_alunos_usuarios` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `alunos_has_oferta`
--
ALTER TABLE `alunos_has_oferta`
  ADD CONSTRAINT `fk_alunos_has_oferta_alunos1` FOREIGN KEY (`alunos_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `fk_alunos_has_oferta_oferta1` FOREIGN KEY (`oferta_id`) REFERENCES `oferta` (`id`);

--
-- Restrições para tabelas `aula`
--
ALTER TABLE `aula`
  ADD CONSTRAINT `fk_aula_oferta1` FOREIGN KEY (`oferta_id`) REFERENCES `oferta` (`id`);

--
-- Restrições para tabelas `lista_frequencia`
--
ALTER TABLE `lista_frequencia`
  ADD CONSTRAINT `fk_lista_frequencia_alunos1` FOREIGN KEY (`alunos_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `fk_lista_frequencia_aula1` FOREIGN KEY (`aula_id`) REFERENCES `aula` (`id`);

--
-- Restrições para tabelas `oferta`
--
ALTER TABLE `oferta`
  ADD CONSTRAINT `fk_oferta_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `professor`
--
ALTER TABLE `professor`
  ADD CONSTRAINT `fk_professor_oferta1` FOREIGN KEY (`oferta_id`) REFERENCES `oferta` (`id`),
  ADD CONSTRAINT `fk_professor_usuarios1` FOREIGN KEY (`usuarios_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_tipo_usuario` FOREIGN KEY (`tipo_usuario_id`) REFERENCES `tipo_usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
