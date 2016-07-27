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
