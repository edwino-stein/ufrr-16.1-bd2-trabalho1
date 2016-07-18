-- Tabela das despesas e receitas fixas do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.despesas_receitas_mes
(
   financas_id integer NOT NULL,
   FOREIGN KEY (financas_id) REFERENCES public.financas (id) ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.dispesas_receitas)
WITH (
  OIDS = FALSE
);

ALTER TABLE despesas_receitas_mes
  ADD PRIMARY KEY (id);

ALTER TABLE public.despesas_receitas_mes
  OWNER TO db2;
