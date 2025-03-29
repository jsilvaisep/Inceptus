CREATE PROCEDURE InsertComments()
BEGIN
    DECLARE done BOOLEAN DEFAULT FALSE;
    DECLARE v_user BINARY(36);
    DECLARE v_company BINARY(36);
    DECLARE v_product BINARY(36);
    -- Cursors para percorrer usuários, empresas e produtos
    DECLARE list_user CURSOR FOR SELECT DISTINCT USER_ID FROM USER;
    DECLARE list_company CURSOR FOR SELECT DISTINCT COMPANY_ID FROM COMPANY;
    DECLARE list_product CURSOR FOR SELECT DISTINCT PRODUCT_ID FROM PRODUCT;
    -- Um único handler para todos os cursores
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    -- Loop para usuários
    OPEN list_user;
    user_loop: LOOP
        FETCH list_user INTO v_user;
        IF done THEN
            SET done = FALSE; -- Reset para o próximo cursor
            LEAVE user_loop;
        END IF;
        -- Loop para empresas
        OPEN list_company;
        company_loop: LOOP
            FETCH list_company INTO v_company;
            IF done THEN
                SET done = FALSE;
                LEAVE company_loop;
            END IF;
            -- Loop para produtos
            OPEN list_product;
            product_loop: LOOP
                FETCH list_product INTO v_product;
                IF done THEN
                    SET done = FALSE;
                    LEAVE product_loop;
                END IF;
                -- Inserindo os comentários
                INSERT INTO COMMENT (COMMENT_ID, USER_ID, COMPANY_ID, PRODUCT_ID, COMMENT_RANK, COMMENT_TEXT)
                VALUES (UUID(), v_user, v_company, v_product, FLOOR(RAND() * 6), 'Excelente qualidade e desempenho!');

                INSERT INTO COMMENT (COMMENT_ID, USER_ID, COMPANY_ID, PRODUCT_ID, COMMENT_RANK, COMMENT_TEXT)
                VALUES (UUID(), v_user, v_company, v_product, FLOOR(RAND() * 6),'Atendeu às expectativas, mas poderia ser melhor em alguns aspectos.');
            END LOOP;
            CLOSE list_product;
        END LOOP;
        CLOSE list_company;
    END LOOP;
    CLOSE list_user;

    COMMIT;
END


CALL InsertComments();