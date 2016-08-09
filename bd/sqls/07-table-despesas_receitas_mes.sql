-- Tabela das despesas e receitas fixas do usuário
-- Gerado no Postgresql 9.5

CREATE TABLE public.despesas_receitas_mes
(
   financas_id integer NOT NULL,
   FOREIGN KEY (financas_id) REFERENCES public.financas (id) ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (public.despesas_receitas)
WITH (
  OIDS = FALSE
);

ALTER TABLE despesas_receitas_mes ADD PRIMARY KEY (id);

ALTER TABLE public.despesas_receitas_mes OWNER TO db2;

-- View que realiza um join com a tabela financas
CREATE OR REPLACE VIEW despesas_receitas_mes_view AS
    SELECT
        despesas_receitas_mes.id,
        despesas_receitas_mes.descricao,
        despesas_receitas_mes.valor,
        despesas_receitas_mes.financas_id,
        financas.mes,
        financas.usuario_id
    FROM
        despesas_receitas_mes,
        financas
    WHERE
        despesas_receitas_mes.financas_id = financas.id;

ALTER TABLE despesas_receitas_mes_view OWNER TO db2;
