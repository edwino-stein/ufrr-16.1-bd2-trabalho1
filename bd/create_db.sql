
-- Usuário: db2
-- Senha: 123456
CREATE ROLE db2 LOGIN
  ENCRYPTED PASSWORD 'md52304e6df8066c7813fcdc97ea32649a3'
  NOSUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;

CREATE DATABASE bd2_trabalho1
WITH OWNER = db2
     ENCODING = 'UTF8'
     TABLESPACE = pg_default
     CONNECTION LIMIT = -1;