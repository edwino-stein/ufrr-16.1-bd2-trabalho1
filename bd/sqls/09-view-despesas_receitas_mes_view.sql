-- View que realiza um join com a tabela financas
-- Gerado no Postgresql 9.5

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
    ORDER BY despesas_receitas_mes.valor DESC

ALTER TABLE despesas_receitas_mes_view OWNER TO db2;
