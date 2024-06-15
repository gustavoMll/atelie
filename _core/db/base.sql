-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Maio-2024 às 15:31
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
-- Banco de dados: `tracoengenharia`
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
-- Estrutura da tabela `configs`
--

CREATE TABLE `configs` (
  `chave` varchar(50) NOT NULL,
  `valor` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contas`
--

CREATE TABLE `contas` (
  `id` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL DEFAULT 0,
  `descricao` varchar(255) DEFAULT NULL,
  `numero_nf` varchar(50) DEFAULT NULL,
  `tipo` int(1) NOT NULL,
  `status` int(1) NOT NULL,
  `vencimento` date NOT NULL,
  `pagamento` date DEFAULT NULL,
  `valor` float(11,2) NOT NULL DEFAULT 0.00,
  `ref` varchar(6) DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contatos`
--

CREATE TABLE `contatos` (
  `id` int(11) NOT NULL,
  `id_pessoa` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `setor` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tel` varchar(18) NOT NULL,
  `tel2` varchar(18) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
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
-- Estrutura da tabela `movimentacoes`
--

CREATE TABLE `movimentacoes` (
  `id` int(11) NOT NULL,
  `tipo` int(1) NOT NULL DEFAULT 1,
  `id_conta` int(11) NOT NULL,
  `id_projeto` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `data` date NOT NULL,
  `valor` float(11,2) NOT NULL DEFAULT 0.00,
  `juro` float(11,2) NOT NULL DEFAULT 0.00,
  `multa` float(11,2) NOT NULL DEFAULT 0.00,
  `desconto` float(11,2) NOT NULL DEFAULT 0.00,
  `adicional` float(11,2) NOT NULL DEFAULT 0.00,
  `obs` text DEFAULT NULL,
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
  `nomefantasia` varchar(255) DEFAULT NULL,
  `razaosocial` varchar(255) DEFAULT NULL,
  `cpfcnpj` varchar(14) DEFAULT NULL,
  `ie` varchar(20) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `im` varchar(20) DEFAULT NULL,
  `sexo` int(1) NOT NULL DEFAULT 1,
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `dt_nasc` date DEFAULT NULL,
  `tel` varchar(18) DEFAULT NULL,
  `tel2` varchar(18) DEFAULT NULL,
  `tel3` varchar(18) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `obs` text DEFAULT NULL,
  `status` int(2) NOT NULL,
  `tipo` int(1) NOT NULL DEFAULT 1,
  `ativo` int(1) DEFAULT 1,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `projetos`
--

CREATE TABLE `projetos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor` float(11,2) DEFAULT NULL,
  `area` float(11,2) DEFAULT NULL,
  `dt_ini` date DEFAULT NULL,
  `dt_entrega` date DEFAULT NULL,
  `status` int(1) DEFAULT 1,
  `ativo` int(1) DEFAULT 1,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `login` varchar(20) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `tel` varchar(20) NOT NULL,
  `acesso_total` int(11) DEFAULT NULL,
  `ativo` int(1) DEFAULT 1,
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

INSERT INTO `usuarios` (`id`, `nome`, `login`, `senha`, `email`, `img`, `tel`, `acesso_total`, `ativo`, `ip`, `token`, `ultimo_acesso`, `tentativas`, `ultima_tentativa`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Eliemar Junior', 'ejunior', '2a1b3cb0216a9b7f6f1ae50a9d0c11ff11ce915e', 'eliemar@levsistemas.com.br', NULL, '27998712202', 1, 1, NULL, NULL, NULL, 0, NULL, 'base', '2024-05-30 08:07:45', 'base', '2024-05-30 08:07:45'),
(2, 'Lev Sistemas', 'levsistemas', '41770fd39745bbab0970cbbc30f8b02e48109eba', 'contato@levsistemas.com.br', '', '2740422406', 1, 1, '::1', '', '2024-05-30 08:10:04', 0, NULL, 'base', '2024-05-30 08:07:45', 'site', '2024-05-30 08:10:04');

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
-- Índices para tabela `contas`
--
ALTER TABLE `contas`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`id`);

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
-- Índices para tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
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
-- Índices para tabela `projetos`
--
ALTER TABLE `projetos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de tabela `contas`
--
ALTER TABLE `contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos`
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
-- AUTO_INCREMENT de tabela `movimentacoes`
--
ALTER TABLE `movimentacoes`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `projetos`
--
ALTER TABLE `projetos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
