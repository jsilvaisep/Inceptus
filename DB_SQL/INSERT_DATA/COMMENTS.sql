select * from COMMENT c WHERE PRODUCT_ID = 12;

CREATE PROCEDURE InsertComments()
BEGIN
    DECLARE v_user_id INT DEFAULT 1;
    DECLARE v_company_id INT DEFAULT 1;
    DECLARE v_product_id INT DEFAULT 1;
    -- Loop para USER_ID (1 a 73)
    WHILE v_user_id <= 73 DO
        -- Loop para COMPANY_ID (1 a 40)
        SET v_company_id = 2;
        WHILE v_company_id <= 40 DO
            -- Loop para PRODUCT_ID (1 a 74)
            SET v_product_id = 2;
            WHILE v_product_id <= 76 DO
                -- Inserir os dois comentários
                INSERT INTO COMMENT (USER_ID, COMPANY_ID, PRODUCT_ID, COMMENT_TEXT)
                VALUES
                    (v_user_id, v_company_id, v_product_id, 
                     CONCAT('Comentário positivo para o produto ', v_product_id, ' da empresa ', v_company_id, '. Excelente qualidade e desempenho!'));
  
                INSERT INTO COMMENT (USER_ID, COMPANY_ID, PRODUCT_ID, COMMENT_TEXT)
                VALUES
                    (v_user_id, v_company_id, v_product_id, 
                     CONCAT('Comentário alternativo para o produto ', v_product_id, ' da empresa ', v_company_id, '. Atendeu às expectativas, mas poderia ser melhor em alguns aspectos.'));
                -- Incrementar PRODUCT_ID
                SET v_product_id = v_product_id + 1;
            END WHILE;
            -- Incrementar COMPANY_ID
            SET v_company_id = v_company_id + 1;
        END WHILE;
        -- Incrementar USER_ID
        SET v_user_id = v_user_id + 1;
    END WHILE;
END;

-- DROP PROCEDURE InsertComments;

CALL InsertComments();


