create database SysSec;
use SysSec;

create table seguradora (
id int not null primary key auto_increment,
login varchar(50) not null,
cnpj varchar(25) not null, 
nome varchar(50) not null
);

create table concessionaria (
id int not null primary key auto_increment,
login varchar(50) not null,
cnpj varchar(25) not null, 
nome varchar(50) not null
);