-- Tabela das despesas e receitas fixas do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.dispesas_receitas_fixas
(
   usuario_id integer NOT NULL,
   FOREIGN KEY (usuario_id) REFERENCES public.usuarios (id) ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.dispesas_receitas)
WITH (
  OIDS = FALSE
);

ALTER TABLE dispesas_receitas_fixas
  ADD PRIMARY KEY (id);

ALTER TABLE public.dispesas_receitas_fixas
  OWNER TO db2;
