-- Creo la base de datos
create database hipodromo;
-- Creo usuario carreras con la pass piernodoyuna
create user 'carreras'@'localhost' identified by 'piernodoyuna';
-- Doy permisos al usr carreras
grant select, delete, insert, update, lock tables on hipodromo.* to 'carreras'@'localhost';
