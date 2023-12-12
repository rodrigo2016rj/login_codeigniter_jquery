-- -------------------------------------------------------------------------
-- banco_de_dados_usuarios

DROP SCHEMA IF EXISTS banco_de_dados_usuarios;

CREATE SCHEMA IF NOT EXISTS banco_de_dados_usuarios 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE banco_de_dados_usuarios;

-- -------------------------------------------------------------------------
-- Tabela usuario

DROP TABLE IF EXISTS usuario;

CREATE TABLE IF NOT EXISTS usuario(
  pk_usuario INT NOT NULL AUTO_INCREMENT,
  nome_de_usuario VARCHAR(80) NOT NULL,
  email VARCHAR(160) NOT NULL,
  senha VARCHAR(120) NOT NULL,
  chave_para_operacoes_via_link VARCHAR(30) NOT NULL DEFAULT '',
  momento_do_cadastro DATETIME NOT NULL,
  conta_confirmada ENUM('sim', 'nao') NOT NULL DEFAULT 'nao',
  fuso_horario VARCHAR(100) NOT NULL DEFAULT '-0300',
  visual VARCHAR(100) NOT NULL DEFAULT 'tema_inicial',
  tipo ENUM('comum', 'moderador', 'administrador', 'dono') NOT NULL DEFAULT 'comum',
  sexo ENUM('masculino', 'feminino') NOT NULL,
  exibir_sexo_no_perfil ENUM('sim', 'nao') NOT NULL DEFAULT 'sim',
  exibir_email_no_perfil ENUM('sim', 'nao') NOT NULL DEFAULT 'sim',
  PRIMARY KEY (pk_usuario),
  UNIQUE INDEX nome_de_usuario_UNICA (nome_de_usuario ASC),
  UNIQUE INDEX email_UNICA (email ASC)
)
ENGINE = InnoDB;

-- -------------------------------------------------------------------------
-- Dados de exemplo:

-- Usuários:
INSERT INTO usuario (nome_de_usuario, email, senha, momento_do_cadastro, conta_confirmada, tipo, sexo) VALUES
('usuario_um', 'usuario_um@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:01:00', 'sim', 'dono', 'masculino'),
('usuario_dois', 'usuario_dois@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:02:00', 'sim', 'comum', 'feminino'),
('usuario_tres', 'usuario_tres@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:03:00', 'sim', 'comum', 'masculino'),
('usuario_quatro', 'usuario_quatro@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:04:00', 'sim', 'administrador', 'masculino'),
('usuario_cinco', 'usuario_cinco@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:05:00', 'sim', 'moderador', 'masculino'),
('usuario_seis', 'usuario_seis@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:06:00', 'sim', 'comum', 'feminino'),
('usuario_sete', 'usuario_sete@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:07:00', 'sim', 'comum', 'feminino'),
('usuario_oito', 'usuario_oito@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:08:00', 'sim', 'comum', 'masculino'),
('usuario_nove', 'usuario_nove@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:09:00', 'sim', 'comum', 'feminino'),
('usuario_dez', 'usuario_dez@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:10:00', 'sim', 'comum', 'masculino'),
('usuario_onze', 'usuario_onze@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:11:00', 'sim', 'moderador', 'masculino'),
('usuario_doze', 'usuario_doze@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:12:00', 'sim', 'moderador', 'feminino'),
('usuario_treze', 'usuario_treze@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:13:00', 'sim', 'comum', 'feminino'),
('usuario_quatorze', 'usuario_quatorze@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:14:00', 'sim', 'comum', 'masculino'),
('usuario_quinze', 'usuario_quinze@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:15:00', 'sim', 'comum', 'masculino'),
('usuario_dezesseis', 'usuario_dezesseis@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:16:00', 'sim', 'moderador', 'feminino'),
('usuario_dezessete', 'usuario_dezessete@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:17:00', 'sim', 'comum', 'feminino'),
('usuario_dezoito', 'usuario_dezoito@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:18:00', 'sim', 'moderador', 'masculino'),
('usuario_dezenove', 'usuario_dezenove@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:19:00', 'sim', 'moderador', 'feminino'),
('usuario_vinte', 'usuario_vinte@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:20:00', 'sim', 'administrador', 'feminino'),
('usuario_vinte_um', 'usuario_vinte_um@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:21:00', 'sim', 'comum', 'masculino'),
('usuario_vinte_dois', 'usuario_vinte_dois@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:22:00', 'sim', 'comum', 'feminino'),
('usuario_vinte_tres', 'usuario_vinte_tres@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:23:00', 'sim', 'comum', 'masculino'),
('usuario_vinte_quatro', 'usuario_vinte_quatro@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:24:00', 'sim', 'comum', 'feminino'),
('usuario_vinte_cinco', 'usuario_vinte_cinco@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:25:00', 'sim', 'comum', 'masculino'),
('usuario_vinte_seis', 'usuario_vinte_seis@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:26:00', 'sim', 'administrador', 'feminino'),
('usuario_vinte_sete', 'usuario_vinte_sete@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:27:00', 'sim', 'administrador', 'feminino'),
('usuario_vinte_oito', 'usuario_vinte_oito@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:28:00', 'sim', 'administrador', 'masculino'),
('usuario_vinte_nove', 'usuario_vinte_nove@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:29:00', 'sim', 'comum', 'feminino'),
('usuario_trinta', 'usuario_trinta@emailfalso.rds', '$2y$10$uTTzpeFeeaH/KiegfFqj5.I8D1ElKeFyK8GL3RlFgiqQAnXVoSlyG', '2023-04-16 19:30:00', 'sim', 'comum', 'masculino');
