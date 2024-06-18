-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 19-Jun-2024 às 00:21
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
-- Estrutura da tabela `acessorios`
--

CREATE TABLE `acessorios` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `id_tipo` int(2) NOT NULL,
  `preco` float(11,2) NOT NULL,
  `qtd_total` float(11,2) NOT NULL,
  `qtd_disp` float(11,2) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `ativo` int(1) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `acessorios`
--

INSERT INTO `acessorios` (`id`, `descricao`, `id_tipo`, `preco`, `qtd_total`, `qtd_disp`, `foto`, `ativo`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Teste', 1, 50.00, 14.00, 14.00, 'img_mulherpng.webp', 1, 'admin', '2024-06-15 15:04:02', 'admin', '2024-06-15 15:06:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `alugueis`
--

CREATE TABLE `alugueis` (
  `id` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `dt_aluguel` date NOT NULL,
  `dt_uso` date NOT NULL,
  `dt_prazo` date NOT NULL,
  `dt_entrega` date NOT NULL,
  `local_uso` varchar(255) NOT NULL,
  `valor_aluguel` float(11,2) NOT NULL,
  `valor_entrada` float(11,2) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `alugueis`
--

INSERT INTO `alugueis` (`id`, `id_pedido`, `dt_aluguel`, `dt_uso`, `dt_prazo`, `dt_entrega`, `local_uso`, `valor_aluguel`, `valor_entrada`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 1718743930, '2024-06-18', '2024-06-22', '2024-06-29', '0000-00-00', 'IFES', 140.00, 50.00, 'admin', '2024-06-18 17:56:33', 'admin', '2024-06-18 17:56:33');

-- --------------------------------------------------------

--
-- Estrutura da tabela `alugueispedido`
--

CREATE TABLE `alugueispedido` (
  `id_aluguel` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Extraindo dados da tabela `clientes`
--

INSERT INTO `clientes` (`id`, `id_pessoa`, `obs`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 2, '', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00');

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
-- Estrutura da tabela `documentos`
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

--
-- Extraindo dados da tabela `documentos`
--

INSERT INTO `documentos` (`id`, `descricao`, `obrigatorio`, `arquivo`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'xsdsd', 1, 'LOGO-INOVACOL-BRANCO-2048x669.png', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00'),
(2, 'Teste doc', 0, '0000014_regular_logo-castelo-branco-sem-fundo.webp', '', '0000-00-00 00:00:00', '', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentospedido`
--

CREATE TABLE `documentospedido` (
  `id_documento` int(11) NOT NULL,
  `id_pedido` int(11) NOT NULL
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
-- Estrutura da tabela `fantasias`
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

--
-- Extraindo dados da tabela `fantasias`
--

INSERT INTO `fantasias` (`id`, `descricao`, `id_tipo`, `preco`, `tamanho`, `foto`, `ativo`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Fantasia de noiva', 1, 80.00, 'Adulto', 'img_0000005_regular_paisagem.webp', 1, 'admin', '2024-06-17 18:32:29', 'admin', '2024-06-17 18:32:29');

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
-- Estrutura da tabela `itensaluguel`
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

--
-- Extraindo dados da tabela `itensaluguel`
--

INSERT INTO `itensaluguel` (`id`, `id_item`, `id_aluguel`, `tipo_item`, `qtd`, `modificar`, `obs`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(12, 1, 1, 1, 3, 0, '', 'admin', '2024-06-18 17:56:02', 'admin', '2024-06-18 17:56:33'),
(11, 1, 1, 2, 1, 0, '', 'admin', '2024-06-18 17:55:44', 'admin', '2024-06-18 17:56:33'),
(10, 1, 3, 2, 1, 0, '', 'admin', '2024-06-18 16:51:44', 'admin', '2024-06-18 16:51:44'),
(9, 1, 3, 1, 3, 1, '<p>asas</p>', 'admin', '2024-06-18 16:51:21', 'admin', '2024-06-18 16:51:21'),
(8, 1, 3, 1, 3, 1, '<p>12123</p>', 'admin', '2024-06-18 16:46:28', 'admin', '2024-06-18 16:48:51');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` float(11,2) NOT NULL,
  `forma_pag` int(1) NOT NULL,
  `data` date NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_cliente`, `total`, `forma_pag`, `data`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 2, 14.00, 1, '2024-06-18', 'admin', '2024-06-18 18:00:33', 'admin', '2024-06-18 19:19:19');

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

--
-- Extraindo dados da tabela `permissoes`
--

INSERT INTO `permissoes` (`id`, `nome`, `modulo`, `ativo`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'clientes', 'clientes', 1, 'admin', '2024-06-15 12:09:37', 'admin', '2024-06-15 12:09:37');

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
(1, 'Administrador', '11111111111', '0000-00-00', '', NULL, '', NULL, '', 0, '', NULL, NULL, NULL, NULL, '0', '2024-06-15 11:51:33', '0', '2024-06-15 00:00:00'),
(2, 'Gustavo ', '13518893700', '2000-08-06', '27999999999', '', '', '29700480', 'Rua José Barroso', 3, 'São Vicente', 'Colatina', 'ES', '', '', 'admin', '2024-06-17 20:03:34', 'admin', '2024-06-17 20:03:34');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipos`
--

CREATE TABLE `tipos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usr_cad` varchar(20) NOT NULL,
  `dt_cad` datetime NOT NULL,
  `usr_ualt` varchar(20) NOT NULL,
  `dt_ualt` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipos`
--

INSERT INTO `tipos` (`id`, `nome`, `usr_cad`, `dt_cad`, `usr_ualt`, `dt_ualt`) VALUES
(1, 'Quadrilha', 'admin', '2024-06-15 12:25:01', 'admin', '2024-06-15 13:18:24');

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
(1, 1, 'admin', 'e2178678cf061c82af8d6aee9a0c592b4ef1db60', 1, '::1', NULL, '2024-06-18 14:33:44', 0, NULL, '', '2024-06-15 10:59:28', 'site', '2024-06-18 14:33:44');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `acessorios`
--
ALTER TABLE `acessorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo` (`id_tipo`);

--
-- Índices para tabela `alugueis`
--
ALTER TABLE `alugueis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pedido` (`id_pedido`);

--
-- Índices para tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pessoa` (`id_pessoa`);

--
-- Índices para tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `estaticos`
--
ALTER TABLE `estaticos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `fantasias`
--
ALTER TABLE `fantasias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tipo` (`id_tipo`);

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
-- Índices para tabela `itensaluguel`
--
ALTER TABLE `itensaluguel`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

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
-- Índices para tabela `tipos`
--
ALTER TABLE `tipos`
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
-- AUTO_INCREMENT de tabela `acessorios`
--
ALTER TABLE `acessorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `alugueis`
--
ALTER TABLE `alugueis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `arquivos`
--
ALTER TABLE `arquivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `estaticos`
--
ALTER TABLE `estaticos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fantasias`
--
ALTER TABLE `fantasias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `permissoes`
--
ALTER TABLE `permissoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `permissoesusuario`
--
ALTER TABLE `permissoesusuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `tipos`
--
ALTER TABLE `tipos`
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
