-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/06/2024 às 18:00
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Banco de dados: `atelie`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acessorios`
--

CREATE TABLE `acessorios` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `id_tipo` int(2) NOT NULL,
  `preco` float(11,2) NOT NULL,
  `qtd_total` int(11) NOT NULL,
  `qtd_disp` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `ativo` int(1) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `alugueis`
--

CREATE TABLE `alugueis` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `dt_uso` date NOT NULL,
  `dt_prazo` date NOT NULL,
  `dt_entrega` date NOT NULL,
  `local_uso` varchar(255) NOT NULL,
  `valor_aluguel` float(11,2) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `id` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `id_pessoa` int(11) NOT NULL,
  `obs` text DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configs`
--

CREATE TABLE `configs` (
  `chave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `obrigatorio` int(1) DEFAULT 1,
  `arquivo` varchar(255) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentospedido`
--

CREATE TABLE `documentospedido` (
  `id_documento` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estaticos`
--

CREATE TABLE `estaticos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `meta_keys` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `ativo` int(1) DEFAULT 1,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fantasias`
--

CREATE TABLE `fantasias` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `id_tipo` int(2) NOT NULL,
  `preco` float(11,2) NOT NULL,
  `tamanho` varchar(50) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `ativo` int(1) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos`
--

CREATE TABLE `fotos` (
  `id` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `ordem` int(11) DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico`
--

CREATE TABLE `historico` (
  `id` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `data` datetime NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itensaluguel`
--

CREATE TABLE `itensaluguel` (
  `id` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `id_aluguel` int(11) NOT NULL,
  `tipo_item` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `modificar` int(1) DEFAULT 0,
  `obs` text DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` float(11,2) NOT NULL,
  `forma_pag` int(1) NOT NULL,
  `data` date NOT NULL,
  `valor_entrada` float(11,2) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoes`
--

CREATE TABLE `permissoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `modulo` varchar(20) NOT NULL,
  `ativo` int(1) DEFAULT 1,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissoesusuario`
--

CREATE TABLE `permissoesusuario` (
  `id` int(11) NOT NULL,
  `id_permissao` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `sel` int(1) DEFAULT 1,
  `ins` int(1) DEFAULT 1,
  `upd` int(1) DEFAULT 1,
  `del` int(1) DEFAULT 1,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `dt_nasc` date DEFAULT NULL,
  `telefone1` varchar(20) NOT NULL,
  `telefone2` varchar(20) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `endereco` varchar(255) NOT NULL,
  `numero` int(11) NOT NULL,
  `bairro` varchar(255) NOT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `referencia` varchar(255) DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `cpf`, `dt_nasc`, `telefone1`, `telefone2`, `email`, `cep`, `endereco`, `numero`, `bairro`, `cidade`, `estado`, `complemento`, `referencia`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Administrador', '11111111111', '0000-00-00', '', NULL, '', NULL, '', 0, '', NULL, NULL, NULL, NULL, '0', '2024-06-15 11:51:33', '0', '2024-06-15 00:00:00'),
(2, 'Gustavo ', '13518893700', '2000-08-06', '27999999999', '', '', '29700480', 'Rua José Barroso', 3, 'São Vicente', 'Colatina', 'ES', '', '', 'admin', '2024-06-17 20:03:34', 'admin', '2024-06-17 20:03:34'),
(3, 'Gustavo Marianelli', '13518893700', '2000-08-06', '27995706191', '', '', '27900140', 'Travessa José Toldedo', 101, 'Centro', 'Colatina', 'ES', '', '', 'admin', '2024-06-22 11:51:54', 'admin', '2024-06-22 11:51:54'),
(4, 'André Masioli', '11111111111', '2000-01-01', '27999999999', '', '', '29700050', 'Rua Adamastor Salvador', 100, 'Centro', 'Colatina', 'ES', '', '', 'admin', '2024-06-23 11:53:15', 'admin', '2024-06-23 11:53:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `id_pessoa` int(11) NOT NULL,
  `login` varchar(20) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `acesso_total` int(11) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `token` varchar(20) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `tentativas` int(1) DEFAULT 0,
  `ultima_tentativa` datetime DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_pessoa`, `login`, `senha`, `acesso_total`, `ip`, `token`, `ultimo_acesso`, `tentativas`, `ultima_tentativa`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 1, 'admin', 'e2178678cf061c82af8d6aee9a0c592b4ef1db60', 1, '::1', NULL, '2024-06-18 14:33:44', 0, NULL, '', '2024-06-15 10:59:28', 'site', '2024-06-18 14:33:44');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acessorios`
--
ALTER TABLE `acessorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Índices de tabela `alugueis`
--
ALTER TABLE `alugueis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Índices de tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `estaticos`
--
ALTER TABLE `estaticos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fantasias`
--
ALTER TABLE `fantasias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Índices de tabela `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `itensaluguel`
--
ALTER TABLE `itensaluguel`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `permissoesusuario`
--
ALTER TABLE `permissoesusuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acessorios`
--
ALTER TABLE `acessorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `alugueis`
--
ALTER TABLE `alugueis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `arquivos`
--
ALTER TABLE `arquivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estaticos`
--
ALTER TABLE `estaticos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fantasias`
--
ALTER TABLE `fantasias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itensaluguel`
--
ALTER TABLE `itensaluguel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissoesusuario`
--
ALTER TABLE `permissoesusuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;
