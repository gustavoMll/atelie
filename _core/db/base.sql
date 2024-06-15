-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15-Jun-2024 às 16:53
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atelie`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `arquivos`
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
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `tipo` int(1) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `clientes`
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
-- Estrutura da tabela `configs`
--

CREATE TABLE `configs` (
  `chave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `estaticos`
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
-- Estrutura da tabela `fotos`
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
-- Estrutura da tabela `historico`
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
-- Estrutura da tabela `permissoes`
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
-- Estrutura da tabela `permissoesusuario`
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
-- Estrutura da tabela `pessoas`
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
-- Extraindo dados da tabela `pessoas`
--

INSERT INTO `pessoas` (`id`, `nome`, `cpf`, `dt_nasc`, `telefone1`, `telefone2`, `email`, `cep`, `endereco`, `numero`, `bairro`, `cidade`, `estado`, `complemento`, `referencia`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Administrador', '11111111111', '0000-00-00', '', NULL, '', NULL, '', 0, '', NULL, NULL, NULL, NULL, '0', '2024-06-15 11:51:33', '0', '2024-06-15 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
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
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `id_pessoa`, `login`, `senha`, `acesso_total`, `ip`, `token`, `ultimo_acesso`, `tentativas`, `ultima_tentativa`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 1, 'admin', 'e2178678cf061c82af8d6aee9a0c592b4ef1db60', 1, NULL, NULL, NULL, 0, NULL, '', '2024-06-15 10:59:28', '', '2024-06-15 10:59:28'),
(2, 3, 'teste', 'e2178678cf061c82af8d6aee9a0c592b4ef1db60', 0, '', NULL, NULL, 0, NULL, 'admin', '2024-06-15 11:24:42', 'admin', '2024-06-15 11:36:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- Índices para tabela `estaticos`
--
ALTER TABLE `estaticos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `permissoes`
--
ALTER TABLE `permissoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `permissoesusuario`
--
ALTER TABLE `permissoesusuario`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arquivos`
--
ALTER TABLE `arquivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estaticos`
--
ALTER TABLE `estaticos`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
