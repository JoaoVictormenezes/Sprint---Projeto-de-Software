create database syssec;
use syssec;

create table empresa (
id int not null primary key auto_increment,
senha varchar(50) not null,
cnpj varchar(22) not null, 
nome varchar(50) not null,
email varchar(50) not null
);
truncate table empresa;
drop table empresa;
select	id, nome,cnpj,email,senha from empresa;	

create table perfil(
id int primary key not null,
nome varchar(100) not null, 
imagem varchar(255)  not null,
bio text  not null,
local varchar(90) not null,
foreign key (id) references empresa(id)
);
drop table perfil;
select id,nome,imagem,bio,local from perfil;

update perfil set nome = "tchais",imagem = "../uploads/gragola.jpeg"  where id = 4;
CREATE TABLE perfis_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    perfil_id INT NOT NULL,
    CONSTRAINT fk_user_email FOREIGN KEY (user_email) REFERENCES users(email),
    CONSTRAINT fk_perfil_id FOREIGN KEY (perfil_id) REFERENCES perfil(id)
);
truncate table perfis_salvos;
select * from perfis_salvos;


