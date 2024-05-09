create database SysSec;
use syssec;

create table empresa (
id int not null primary key auto_increment,
senha varchar(50) not null,
cnpj varchar(22) not null, 
nome varchar(50) not null,
email varchar(50) not null
);
