/* 
####################################
####### CALC AVG #######################################################################################################################################################################
####################################
*/

-- RANK DA EMPRESA
SELECT COMPANY_RANK FROM COMPANY WHERE COMPANY_ID = '${COMPANY_ID}';

CREATE PROCEDURE UPD_COMPANY_RANK()
BEGIN
    DECLARE DONE BOOLEAN DEFAULT FALSE;
    DECLARE COMPANY BINARY(36);
    DECLARE LIST_COMPANY CURSOR FOR SELECT DISTINCT COMPANY_ID FROM COMPANY;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET DONE = TRUE;
    OPEN LIST_COMPANY;
	    READ_LOOP: LOOP
	        FETCH LIST_COMPANY INTO COMPANY;
	        IF DONE THEN
	            LEAVE READ_LOOP;
	        END IF;
	        UPDATE COMPANY 
			SET COMPANY_RANK = (SELECT IFNULL(AVG(COMMENT_RANK), 0.0) FROM COMMENT WHERE COMPANY_ID = COMPANY)
			WHERE COMPANY_ID = COMPANY	
			AND COMPANY_STATUS = 'A';
	    END LOOP;  
    CLOSE LIST_COMPANY;
    COMMIT;
END;

DROP PROCEDURE UPD_COMPANY_RANK;
-- RANK DO PRODUTO

SELECT PRODUCT_RANK FROM PRODUCT WHERE PRODUCT_ID = '${PRODUCT_ID}';

CREATE PROCEDURE UPD_PRODUCT_RANK()
BEGIN
    DECLARE DONE BOOLEAN DEFAULT FALSE;
    DECLARE PRODUCT BINARY(36);
    DECLARE LIST_PRODUCT CURSOR FOR SELECT DISTINCT PRODUCT_ID FROM PRODUCT;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET DONE = TRUE;
    OPEN LIST_PRODUCT;
	    READ_LOOP: LOOP
	        FETCH LIST_PRODUCT INTO PRODUCT;
	        IF DONE THEN
	            LEAVE READ_LOOP;
	        END IF;
	        UPDATE PRODUCT 
			SET PRODUCT_RANK = (SELECT IFNULL(AVG(COMMENT_RANK), 0.0) FROM COMMENT WHERE PRODUCT_ID = PRODUCT)
			WHERE PRODUCT_ID = PRODUCT
			AND PRODUCT_STATUS = 'A';
	    END LOOP;  
    CLOSE LIST_PRODUCT;
    COMMIT;
END;

DROP PROCEDURE UPD_PRODUCT_RANK;

/* 
####################################
####### PIN RESET PASSWORD #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE CREATE_RESET_CODE (IN V_USER_ID INT)
BEGIN
	INSERT INTO DB_INCEPTUS_PP.SEC_RST_CODE
	(USER_ID, GENERATED_CODE)
	VALUES(V_USER_ID,FLOOR(100000 + RAND() * 900000));
END

/* 
####################################
####### TOP BY RANK #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE COMPANY_TOP() 
BEGIN
	SELECT c.*, u.USER_NAME FROM COMPANY c 
	INNER JOIN USER u ON u.USER_ID = c.USER_ID
	WHERE c.COMPANY_STATUS = 'A' 
	ORDER BY c.COMPANY_RANK DESC LIMIT 3;
END

DROP PROCEDURE COMPANY_TOP;

CREATE PROCEDURE PRODUCT_TOP()
BEGIN
	SELECT * FROM PRODUCT p 
	INNER JOIN CATEGORY cat ON cat.CATEGORY_ID  = p.CATEGORY_ID 
	WHERE p.PRODUCT_STATUS  = 'A'
	AND cat.CATEGORY_TYPE = 'PRODUTO'
	ORDER BY p.PRODUCT_RANK DESC LIMIT 3;
END

CREATE PROCEDURE SERVICE_TOP()
BEGIN
	SELECT * FROM PRODUCT p 
	INNER JOIN CATEGORY cat ON cat.CATEGORY_ID  = p.CATEGORY_ID 
	WHERE p.PRODUCT_STATUS  = 'A'
	AND cat.CATEGORY_TYPE = 'SERVICO'
	ORDER BY p.PRODUCT_RANK DESC LIMIT 3;
END

/* 
####################################
####### EMPRESAS MAIS RECENTES #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE TRENDING_TOP()
BEGIN
	SELECT p.POST_CONTENT, COM.COMPANY_NAME 
	FROM POST p
	INNER JOIN COMPANY COM ON p.COMPANY_ID = COM.COMPANY_ID
	WHERE COM.COMPANY_STATUS = 'A'
	ORDER BY p.CREATED_AT DESC LIMIT 3;
END

/* 
####################################
####### CRIAR UTILIZADORES #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE INSERT_USER (IN V_USER_NAME VARCHAR(255), IN V_USER_LOGIN VARCHAR(20), IN V_USER_EMAIL VARCHAR(255), IN V_USER_PASSWORD VARCHAR(255), IN V_IMG_URL VARCHAR(255))
BEGIN
	INSERT INTO USER 
	(USER_ID, USER_LOGIN, USER_NAME, USER_EMAIL, USER_PASSWORD, IMG_URL, USER_TYPE_ID) 
	VALUES (UUID(), V_USER_LOGIN, V_USER_NAME, V_USER_EMAIL, V_USER_PASSWORD, V_IMG_URL, '89a9c26a-100b-11f0-ab2e-020017000d59');
END

DROP PROCEDURE INSERT_USER;

/* 
####################################
####### CRIAR EMPRESAS #######################################################################################################################################################################
####################################
*/

CREATE TRIGGER ASSOCIAR_USER_COMPANY
AFTER INSERT ON USER
FOR EACH ROW
BEGIN
    IF NEW.USER_TYPE_ID = '8986b070-100b-11f0-ab2e-020017000d59' THEN
        INSERT INTO COMPANY (USER_ID, COMPANY_ID, COMPANY_NAME, COMPANY_EMAIL) VALUES (NEW.USER_ID, UUID(),  NEW.USER_NAME);
    END IF;
END
-- ----------------------------------------------------------------------------------------- 
DROP TRIGGER ASSOCIAR_USER_COMPANY;

CREATE PROCEDURE INSERT_COMPANY (IN V_USER_NAME VARCHAR(255), IN V_COMPANY_NAME VARCHAR(255), IN V_USER_LOGIN VARCHAR(20), IN V_USER_EMAIL VARCHAR(255), IN V_COMPANY_PASSWORD VARCHAR(255), 
								IN V_IMG_URL VARCHAR(255), IN V_COMPANY_EMAIL VARCHAR(255),IN V_COMPANY_SITE VARCHAR(255))
BEGIN
	DECLARE NEW_USER_ID BINARY(36);
	SET NEW_USER_ID = UUID();
	INSERT INTO USER 
	(USER_ID, USER_NAME, USER_LOGIN, USER_EMAIL, USER_PASSWORD, IMG_URL, USER_TYPE_ID) 
	VALUES (NEW_USER_ID , V_USER_NAME,  V_USER_LOGIN, V_USER_EMAIL, V_COMPANY_PASSWORD, V_IMG_URL, '8986b070-100b-11f0-ab2e-020017000d59');

	UPDATE COMPANY c SET c.COMPANY_EMAIL = V_COMPANY_EMAIL, c.COMPANY_SITE = V_COMPANY_SITE, c.COMPANY_NAME = V_COMPANY_NAME
	WHERE c.USER_ID = NEW_USER_ID;
END
-- -----------------------------------------------------------------------------------------
DROP PROCEDURE INSERT_COMPANY;
/* 
####################################
####### CRIAR PRODUTO #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE INSERT_PRODUCT (IN V_PRODUCT_NAME VARCHAR(255), IN V_PRODUCT_DESCRIPTION VARCHAR(1000), IN V_CATEGORY_ID VARCHAR(255), IN V_COMPANY_ID VARCHAR(255))
BEGIN
	INSERT INTO PRODUCT (PRODUCT_ID, PRODUCT_NAME, PRODUCT_DESCRIPTION, CATEGORY_ID, COMPANY_ID)
	VALUES (UUID(), V_PRODUCT_NAME, V_PRODUCT_DESCRIPTION, V_CATEGORY_ID, V_COMPANY_ID);
END

/* 
####################################
####### APAGAR PRODUTO #######################################################################################################################################################################
####################################
*/

CREATE TRIGGER COMMENT_DELETE AFTER UPDATE ON PRODUCT
FOR EACH ROW
BEGIN
	IF NEW.PRODUCT_STATUS = 'I' THEN
	    UPDATE COMMENT c
	    SET c.COMMENT_STATUS = 'I'
	    WHERE PRODUCT_ID = NEW.PRODUCT_ID;
	END IF;
END

CREATE PROCEDURE DELETE_PRODUCT (IN V_PRODUCT_ID BINARY(36))
BEGIN
	UPDATE PRODUCT SET PRODUCT_STATUS='I' WHERE PRODUCT_ID = V_PRODUCT_ID;
END

-- -------------------------------------------------------------------------
DROP PROCEDURE DELETE_PRODUCT;

/* 
####################################
####### CRIAR RESPOSTA A POST #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE INSERT_POST_EXT (IN V_POST_ID VARCHAR(255), IN V_USER_ID BINARY(36), IN V_POST_EXT_CONTENT VARCHAR(1000))
BEGIN
	INSERT INTO DB_INCEPTUS_PP.POST_EXT	(POST_EXT_ID, POST_ID, USER_ID, POST_EXT_CONTENT)
	VALUES(UUID(), V_POST_ID, V_USER_ID, V_POST_EXT_CONTENT);
END
-- --------------------------------------------------------------------------------------------

DROP PROCEDURE INSERT_POST_EXT;


/* 
####################################
####### CRIAR COMENTÁRIO  #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE INSERT_COMMENT (IN V_USER_ID BINARY(36), IN V_COMPANY_ID BINARY(36), IN V_PRODUCT_ID BINARY(36), IN V_COMMENT_RANK INT, IN V_COMMENT_TEXT VARCHAR(2000))
BEGIN
	INSERT INTO COMMENT (COMMENT_ID, USER_ID, COMPANY_ID, PRODUCT_ID, COMMENT_RANK, COMMENT_TEXT)
	VALUES (UUID(), V_USER_ID, V_COMPANY_ID, V_PRODUCT_ID, V_COMMENT_RANK,V_COMMENT_TEXT);
END

DROP PROCEDURE INSERT_COMMENT;


/* 
####################################
####### CRIAR RESPOSTA COMENTÁRIO  #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE INSERT_COMMENT_EXT (IN V_COMMENT_ID BINARY(36), IN V_COMMENT_EXT_TEXT VARCHAR(2000))
BEGIN
	INSERT INTO COMMENT_EXT (COMMENT_EXT_ID, COMMENT_ID, COMMENT_EXT_TEXT)
	VALUES (UUID(), V_COMMENT_ID, V_COMMENT_EXT_TEXT);
END

DROP PROCEDURE INSERT_COMMENT_EXT;
