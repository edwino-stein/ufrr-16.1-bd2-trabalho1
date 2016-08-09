-- Tabela de com generica com as despesas e receitas por mês do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.despesas_receitas
(
  id serial,
  descricao character varying(100) NOT NULL,
  valor real NOT NULL,
  CONSTRAINT despesas_receitas_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.despesas_receitas
  OWNER TO db2;
