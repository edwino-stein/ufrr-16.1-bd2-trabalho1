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
);

ALTER TABLE public.usuarios
  OWNER TO db2;

-- Trigger para tratar a inserção e atualização de usuarios
DROP TRIGGER IF EXISTS on_insert_update_usuario ON usuarios;
CREATE OR REPLACE FUNCTION on_insert_update_usuario() RETURNS trigger AS $on_insert_update_usuario$
	BEGIN

        --Se a operação for um UPDATE
		IF (TG_OP = 'UPDATE') THEN

            -- Se o login foi alterado e o novo não for unico, gera uma exceção unique_violation
			IF(NEW.login <> OLD.login) THEN
				IF((SELECT COUNT(*) FROM usuarios WHERE usuarios.login = NEW.login) >= 1) THEN
					RAISE EXCEPTION 'O usuário já foi cadastrado no banco de dados.' USING ERRCODE = 'unique_violation';
				END IF;
			END IF;

            -- Se a senha foi alterada, gera uma hash MD5
			IF(NEW.senha <> OLD.senha) THEN
				NEW.senha := md5(NEW.senha);
			END IF;

        -- Se a operação for um INSERT
		ELSE

            -- Verifica se o login é unico, se não gera uma exceção unique_violation
			IF((SELECT COUNT(*) FROM usuarios WHERE usuarios.login = NEW.login) >= 1) THEN
				RAISE EXCEPTION 'O usuário já foi cadastrado no banco de dados.' USING ERRCODE = 'unique_violation';
			END IF;

            -- Gera uma hash MD5 para a senha
			NEW.senha := md5(NEW.senha);

		END IF;

		RETURN NEW;
	END;
$on_insert_update_usuario$ LANGUAGE plpgsql;

CREATE TRIGGER on_insert_update_usuario BEFORE INSERT OR UPDATE ON usuarios
    FOR EACH ROW EXECUTE PROCEDURE on_insert_update_usuario();

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
        despesas_receitas_mes.financas_id = financas.id
    ORDER BY despesas_receitas_mes.valor DESC;

ALTER TABLE despesas_receitas_mes_view OWNER TO db2;
