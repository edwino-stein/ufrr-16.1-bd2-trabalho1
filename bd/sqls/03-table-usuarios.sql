-- Tabela de usuários da aplicação
-- Gerado no Postgresql 9.5

CREATE TABLE public.usuarios
(
   id serial,
   nome character varying(50) NOT NULL,
   sobrenome character varying(50),
   login character varying(20) NOT NULL,
   senha character varying(32) NOT NULL,
   PRIMARY KEY (id)
)
WITH (
  OIDS = FALSE
)
;
ALTER TABLE public.usuarios
  OWNER TO db2;
