-- Tabela das despesas e receitas fixas do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.despesas_receitas_fixas
(
   usuario_id integer NOT NULL,
   FOREIGN KEY (usuario_id) REFERENCES public.usuarios (id) ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.despesas_receitas)
WITH (
  OIDS = FALSE
);

ALTER TABLE despesas_receitas_fixas
  ADD PRIMARY KEY (id);

ALTER TABLE public.despesas_receitas_fixas
  OWNER TO db2;
