-- Tabela de com as finanças do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.financas
(
   id serial,
   usuario_id integer NOT NULL,
   mes date NOT NULL,
   PRIMARY KEY (id),
   FOREIGN KEY (usuario_id) REFERENCES public.usuarios (id) ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS = FALSE
);

ALTER TABLE public.financas OWNER TO db2;
