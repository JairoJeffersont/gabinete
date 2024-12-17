CREATE TABLE cliente (
    cliente_id int NOT NULL AUTO_INCREMENT,
    cliente_token varchar(255) NOT NULL,
    cliente_nome varchar(255) NOT NULL,
    cliente_email varchar(255) NOT NULL UNIQUE,
    cliente_telefone varchar(20) NOT NULL,        
    cliente_ativo tinyint(1) NOT NULL,
    cliente_assinaturas int NOT NULL,
    cliente_deputado_id int NOT NULL,
    cliente_deputado_nome varchar(255) NOT NULL,
    cliente_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cliente_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO cliente (cliente_id, cliente_token, cliente_nome, cliente_email, cliente_telefone, cliente_ativo, cliente_assinaturas, cliente_deputado_id, cliente_deputado_nome) VALUES (1,'qqqqqqq','CLIENTE SISTEMA', 'email@email.com', '000000', 1, 2, 00000, 'deputado');


CREATE TABLE usuario (
    usuario_id int NOT NULL AUTO_INCREMENT,
    usuario_nome varchar(255) NOT NULL,
    usuario_email varchar(255) NOT NULL UNIQUE,
    usuario_telefone varchar(20) NOT NULL,
    usuario_senha varchar(255) NOT NULL,
    usuario_nivel int NOT NULL,
    usuario_ativo tinyint(1) NOT NULL,
    usuario_aniversario date NOT NULL,
    usuario_foto varchar(255) DEFAULT NULL,
    usuario_cliente int NOT NULL,
    usuario_criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_atualizado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id),
     CONSTRAINT fk_cliente FOREIGN KEY (usuario_cliente) REFERENCES cliente(cliente_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;


INSERT INTO usuario (usuario_id, usuario_nome, usuario_email, usuario_telefone, usuario_senha, usuario_nivel, usuario_ativo, usuario_aniversario, usuario_cliente) VALUES (1,'USUÁRIO SISTEMA', 'email@email.com', '000000', 'sd9fasdfasd9fasd89fsad9f8', 1, 1, '2000-01-01', 1);
