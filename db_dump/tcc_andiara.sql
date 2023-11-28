-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 27/11/2023 às 21:10
-- Versão do servidor: 8.0.33-cll-lve
-- Versão do PHP: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `willcode_tcc_andiara`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `contrato`
--

CREATE TABLE `contrato` (
  `id` int NOT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'Ativo',
  `dt_inicio` date NOT NULL,
  `dt_termino` date DEFAULT NULL,
  `dt_encerramento` date DEFAULT NULL,
  `dt_aditamento` date DEFAULT NULL,
  `orcamento_inicial` double(10,2) NOT NULL DEFAULT '0.00',
  `saldo_orcamento` double(10,2) NOT NULL DEFAULT '0.00',
  `nome_empresa` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `contrato`
--

INSERT INTO `contrato` (`id`, `numero`, `status`, `dt_inicio`, `dt_termino`, `dt_encerramento`, `dt_aditamento`, `orcamento_inicial`, `saldo_orcamento`, `nome_empresa`) VALUES
(11, '0001', 'Inativo', '2023-01-01', '2024-01-01', '2024-04-01', '2024-05-01', 500000.00, 300000.00, 'Empresa 1'),
(12, '0002', 'Ativo', '2023-01-01', '2024-01-01', NULL, '2024-06-01', 5555555.00, 2555555.00, 'Empresa 2'),
(13, '0003', 'Ativo', '2023-11-01', '2024-11-01', NULL, NULL, 100000.00, 80000.00, 'Empresa 3');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recurso`
--

CREATE TABLE `recurso` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `quantidade` int DEFAULT NULL,
  `validade_meses` int NOT NULL DEFAULT '1',
  `status` varchar(45) NOT NULL DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `recurso`
--

INSERT INTO `recurso` (`id`, `nome`, `tipo`, `quantidade`, `validade_meses`, `status`) VALUES
(6, 'Betoneira', 'Equipamento', 10, 15, 'Ativo'),
(7, 'Sacos de areia', 'Insumo', 200, 5, 'Ativo'),
(8, 'Fiscal de obra - Fulano', 'Pessoa', 1, 12, 'Ativo'),
(9, 'Caminhão Betoneira', 'Veículo', 1, 58, 'Ativo'),
(10, 'Sacos de cimento', 'Insumo', 200, 10, 'Ativo'),
(11, 'Pá', 'Equipamento', 25, 36, 'Ativo'),
(12, 'Técnico de segurança - Ciclano', 'Pessoa', 1, 36, 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recursos_contrato`
--

CREATE TABLE `recursos_contrato` (
  `id` int NOT NULL,
  `id_contrato` int NOT NULL,
  `id_recurso` int NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `status` varchar(45) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `recursos_contrato`
--

INSERT INTO `recursos_contrato` (`id`, `id_contrato`, `id_recurso`, `validade_meses`, `status`) VALUES
(10, 11, 6, NULL, 'Ativo'),
(11, 11, 10, NULL, 'Ativo'),
(12, 11, 8, NULL, 'Ativo'),
(13, 12, 9, NULL, 'Ativo'),
(14, 12, 7, NULL, 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas`
--

CREATE TABLE `tarefas` (
  `id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `opcional_obrigatoria` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'null',
  `status` varchar(45) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `tarefas`
--

INSERT INTO `tarefas` (`id`, `nome`, `validade_meses`, `opcional_obrigatoria`, `status`) VALUES
(6, '1- Análise financeira', NULL, 'Obrigatória', 'Ativo'),
(7, '2- Assinatura do contrato', NULL, 'Obrigatória', 'Ativo'),
(8, '3- Acompanhamento mensal em sítio', 1, 'Obrigatória', 'Ativo'),
(9, '4- Análise de risco trabalhista', 1, 'Opcional', 'Ativo'),
(10, '5- Auditoria de obra', 2, 'Opcional', 'Ativo'),
(11, '6- Fiscalização de equipamentos', 3, 'Opcional', 'Ativo'),
(12, '7- Encerramento de contrato', NULL, 'Opcional', 'Ativo'),
(13, '8- Aditamento de contrato', NULL, 'Opcional', 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tarefas_contrato`
--

CREATE TABLE `tarefas_contrato` (
  `id` int NOT NULL,
  `id_contrato` int NOT NULL,
  `id_tarefa` int NOT NULL,
  `validade_meses` int DEFAULT NULL,
  `status` varchar(45) DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `tarefas_contrato`
--

INSERT INTO `tarefas_contrato` (`id`, `id_contrato`, `id_tarefa`, `validade_meses`, `status`) VALUES
(15, 11, 6, NULL, 'Ativo'),
(16, 11, 7, NULL, 'Ativo'),
(17, 11, 8, 1, 'Ativo'),
(18, 11, 9, NULL, 'Ativo'),
(20, 11, 10, 1, 'Ativo'),
(21, 12, 6, NULL, 'Ativo'),
(22, 12, 7, NULL, 'Ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `upload`
--

CREATE TABLE `upload` (
  `id` int NOT NULL,
  `id_tarefa_contrato` int NOT NULL,
  `arquivo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nome` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `upload`
--

INSERT INTO `upload` (`id`, `id_tarefa_contrato`, `arquivo`, `nome`, `mime_type`) VALUES
(18, 15, '2023-11-28-12-01-5757_15_dummy.pdf', 'dummy.pdf', 'application/pdf'),
(19, 15, '2023-11-28-12-02-0303_15_dummy.pdf', 'dummy.pdf', 'application/pdf'),
(20, 16, '2023-11-28-12-02-3535_16_dummy.pdf', 'dummy.pdf', 'application/pdf'),
(21, 17, '2023-11-28-12-04-2626_17_tcc_diagrama_mer.pdf', 'tcc_diagrama_mer.pdf', 'application/pdf'),
(22, 16, '2023-11-28-12-05-3131_16_caso de uso tcc (1).png', 'caso de uso tcc (1).png', 'image/png'),
(23, 21, '2023-11-28-12-07-4545_21_dummy.pdf', 'dummy.pdf', 'application/pdf');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user`
--

CREATE TABLE `user` (
  `login` varchar(200) NOT NULL COMMENT 'Utilizado para fazer login',
  `email` varchar(150) NOT NULL,
  `password` varchar(250) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `api_token` varchar(200) DEFAULT NULL COMMENT 'Token para autenticação via API'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Cadastro de usuários';

--
-- Despejando dados para a tabela `user`
--

INSERT INTO `user` (`login`, `email`, `password`, `create_date`, `active`, `id`, `name`, `api_token`) VALUES
('teste', 'email@exemplo.com', 'fc94908aacc4ee5f5628b0fda5cb487f', '2023-02-15 13:24:35', 1, 9, 'Usuário Teste', '69c056d40ca6f4e6d21d4c6ff9d3b7ae');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `contrato`
--
ALTER TABLE `contrato`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `recurso`
--
ALTER TABLE `recurso`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `recursos_contrato`
--
ALTER TABLE `recursos_contrato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recursos_contrato_id_contrato_idx` (`id_contrato`),
  ADD KEY `fk_recursos_contrato_id_recurso_idx` (`id_recurso`);

--
-- Índices de tabela `tarefas`
--
ALTER TABLE `tarefas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tarefas_contrato`
--
ALTER TABLE `tarefas_contrato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tarefas_contrato_id_tarefa_idx` (`id_tarefa`),
  ADD KEY `fk_tarefas_contrato_id_contrato_idx` (`id_contrato`);

--
-- Índices de tabela `upload`
--
ALTER TABLE `upload`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_upload_id_tarefa_contrato_idx` (`id_tarefa_contrato`);

--
-- Índices de tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_UN_login` (`login`),
  ADD UNIQUE KEY `user_UN_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contrato`
--
ALTER TABLE `contrato`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `recurso`
--
ALTER TABLE `recurso`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `recursos_contrato`
--
ALTER TABLE `recursos_contrato`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `tarefas`
--
ALTER TABLE `tarefas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `tarefas_contrato`
--
ALTER TABLE `tarefas_contrato`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `upload`
--
ALTER TABLE `upload`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `recursos_contrato`
--
ALTER TABLE `recursos_contrato`
  ADD CONSTRAINT `fk_recursos_contrato_id_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`),
  ADD CONSTRAINT `fk_recursos_contrato_id_recurso` FOREIGN KEY (`id_recurso`) REFERENCES `recurso` (`id`);

--
-- Restrições para tabelas `tarefas_contrato`
--
ALTER TABLE `tarefas_contrato`
  ADD CONSTRAINT `fk_tarefas_contrato_id_contrato` FOREIGN KEY (`id_contrato`) REFERENCES `contrato` (`id`),
  ADD CONSTRAINT `fk_tarefas_contrato_id_tarefa` FOREIGN KEY (`id_tarefa`) REFERENCES `tarefas` (`id`);

--
-- Restrições para tabelas `upload`
--
ALTER TABLE `upload`
  ADD CONSTRAINT `fk_upload_id_tarefa_contrato` FOREIGN KEY (`id_tarefa_contrato`) REFERENCES `tarefas_contrato` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;