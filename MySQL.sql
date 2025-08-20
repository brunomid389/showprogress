DROP DATABASE sinas;

CREATE DATABASE sinas;

USE sinas;

CREATE TABLE comunidade (
    id_comunidade INT PRIMARY KEY AUTO_INCREMENT,
    uf VARCHAR(255) NOT NULL,
    cidade VARCHAR(255) NOT NULL,
    bairro VARCHAR(255) NOT NULL UNIQUE
);

# Por enquanto apenas alguns bairos, cidades etc
INSERT INTO comunidade (uf, cidade, bairro) VALUES
("Pernambuco", "Paulista", "Área Rural de Paulista"),
("Pernambuco", "Paulista", "Artur Lundgren I"),
("Pernambuco", "Paulista", "Artur Lundgren II"),
("Pernambuco", "Paulista", "Aurora"),
("Pernambuco", "Paulista", "Centro"),
("Pernambuco", "Paulista", "Engenho Maranguape"),
("Pernambuco", "Paulista", "Fragoso"),
("Pernambuco", "Paulista", "Jaguarana"),
("Pernambuco", "Paulista", "Jaguaribe"),
("Pernambuco", "Paulista", "Janga"),
("Pernambuco", "Paulista", "Jardim Maranguape"),
("Pernambuco", "Paulista", "Jardim Paulista"),
("Pernambuco", "Paulista", "Maranguape I"),
("Pernambuco", "Paulista", "Maranguape II"),
("Pernambuco", "Paulista", "Maria Farinha"),
("Pernambuco", "Paulista", "Mirueira"),
("Pernambuco", "Paulista", "Nobre"),
("Pernambuco", "Paulista", "Nossa Senhora da Conceição"),
("Pernambuco", "Paulista", "Nossa Senhora do Ó"),
("Pernambuco", "Paulista", "Paratibe"),
("Pernambuco", "Paulista", "Pau Amarelo"),
("Pernambuco", "Paulista", "Poty"),
("Pernambuco", "Paulista", "Tabajara"),
("Pernambuco", "Paulista", "Vila Torres Galvão");

CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(11) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nascimento DATE NOT NULL,
    parente VARCHAR(255) NULL,
    filho VARCHAR(255) NULL,
    id_comunidade INT NOT NULL,
    FOREIGN KEY (id_comunidade) REFERENCES comunidade(id_comunidade)
);

# Valor do id_comunidade vai ser adicionado pelo JDBC
# Uusario cria conta -> JDBC salva valores em variáveis -> JDBC insere variáveis em 'usuario'
INSERT INTO usuario(nome, telefone, email, senha, id_comunidade) VALUES
("Eduardo Passos", "987654", "edu@gmail.com", "123", "1"),
("Bruno Rafael", "123456", "bru@gmail.com", "123", "10"),
("Diógenes Luiz", "654321", "dio@gmail.com", "123", "3");

# Postagens vão possuir tags sobre os locais ou temas que estão relacionados a postagem.
# O usuário pode criar uma tag nova ao adicioná-la a uma postagem
CREATE TABLE tag (
    id_tag INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL UNIQUE
);

INSERT INTO tag(nome) VALUES("Nacional");

# Comunitário e Nacional. Ambos podem ser um projeto (uma iniciativa para fazer alguma mudança) ou post (algo mais informal, compartilhando notícia etc).
CREATE TABLE postagem (
    id_postagem INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    texto VARCHAR(500),
    id_tag INT,
    img_url VARCHAR(500) NULL,
	video_url VARCHAR(500) NULL,
    id_usuario INT NOT NULL,
    id_comunidade INT NOT NULL,
    FOREIGN KEY (id_tag) REFERENCES tag(id_tag),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_comunidade) REFERENCES comunidade(id_comunidade)
);

INSERT INTO postagem(titulo, texto, id_tag, id_usuario, id_comunidade) VALUES("Postagem", "Isso é uma postagem", "1", "1", "1");

 CREATE TABLE chat (
	id_chat INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(255) NOT NULL
);

INSERT INTO chat(tipo) VALUES ("geral"), ("reels"), ("postagem");

# Mensagens que vão ser salvos dentro de um específico tipo de chat.
 CREATE TABLE mensagem (
	id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    texto VARCHAR(500) NOT NULL,
    id_usuario INT NOT NULL,
    id_chat INT NOT NULL,
    id_postagem INT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_chat) REFERENCES chat(id_chat),
    FOREIGN KEY (id_postagem) REFERENCES postagem(id_postagem)
 );
 
SELECT u.id_usuario, u.nome, u.email, u.senha, u.id_comunidade, c.uf, c.cidade, c.bairro
FROM usuario u
INNER JOIN comunidade c
ON u.id_comunidade = c.id_comunidade;

# 'IF' a tag for nacional não vai incluir a comunidade da postagem
SELECT p.id_postagem, p.titulo, p.texto, t.nome AS tag, u.nome AS usuario, c.uf, c.cidade, c.bairro
FROM postagem p
INNER JOIN tag t
ON p.id_tag = t.id_tag
INNER JOIN usuario u
ON p.id_usuario = u.id_usuario
INNER JOIN comunidade c
ON p.id_comunidade = c.id_comunidade;



SELECT * FROM comunidade;
SELECT * FROM usuario;
SELECT * FROM postagem;
SELECT * FROM mensagem;
SELECT * FROM tag;