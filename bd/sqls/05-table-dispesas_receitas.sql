-- Tabela de com generica com as despesas e receitas por mês do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.dispesas_receitas
(
  id serial,
  descricao character varying(100) NOT NULL,
  valor real NOT NULL,
  CONSTRAINT dispesas_receitas_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.dispesas_receitas
  OWNER TO db2;
