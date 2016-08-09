-- Função que inicializa os meses
-- Gerado no Postgresql 9.5

CREATE OR REPLACE FUNCTION init_mes(user_id integer) RETURNS integer AS $init_mes$
  	DECLARE
  		mes_id integer := 0;
  		r despesas_receitas_fixas;
  	BEGIN
  		IF((SELECT COUNT(*) FROM financas WHERE financas.usuario_id = user_id and EXTRACT(MONTH FROM financas.mes) = EXTRACT(MONTH FROM now()) and EXTRACT(YEAR FROM financas.mes) = EXTRACT(YEAR FROM now())) <= 0) THEN

  			INSERT INTO financas (usuario_id, mes) VALUES(1, now());
  			mes_id := (SELECT currval(pg_get_serial_sequence('financas','id')));


  			FOR r IN SELECT * FROM despesas_receitas_fixas WHERE despesas_receitas_fixas.usuario_id = user_id
  			LOOP
  				INSERT INTO despesas_receitas_mes (descricao, valor, financas_id) VALUES(r.descricao, r.valor, mes_id);
  			END LOOP;

  			RETURN 1;
  		END IF;
  		RETURN 0;
  	END;
$init_mes$ LANGUAGE plpgsql;

ALTER FUNCTION init_mes(integer) OWNER TO db2;
