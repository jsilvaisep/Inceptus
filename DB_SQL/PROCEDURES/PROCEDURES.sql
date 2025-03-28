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
    DECLARE COMPANY INT;
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
			WHERE COMPANY_ID = COMPANY;
	    END LOOP;  
    CLOSE LIST_COMPANY;
    COMMIT;
END;

-- RANK DO PRODUTO

SELECT PRODUCT_RANK FROM PRODUCT WHERE PRODUCT_ID = '${PRODUCT_ID}';

CREATE PROCEDURE UPD_PRODUCT_RANK()
BEGIN
    DECLARE DONE BOOLEAN DEFAULT FALSE;
    DECLARE PRODUCT INT;
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
			WHERE PRODUCT_ID = PRODUCT;
	    END LOOP;  
    CLOSE LIST_PRODUCT;
    COMMIT;
END;

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
	SELECT * FROM COMPANY C ORDER BY C.COMPANY_RANK DESC LIMIT 3;
END

CREATE PROCEDURE PRODUCT_TOP()
BEGIN
SELECT P.PRODUCT_NAME, P.PRODUCT_DESCRIPTION, P.IMG_URL  FROM PRODUCT P ORDER BY P.PRODUCT_RANK DESC LIMIT 3;
END

/* 
####################################
####### EMPRESAS MAIS RECENTES #######################################################################################################################################################################
####################################
*/

CREATE PROCEDURE TRENDING_TOP()
BEGIN
	SELECT C.COMMENT_TEXT, COM.COMPANY_NAME 
	FROM COMMENT C
	INNER JOIN COMPANY COM ON C.COMPANY_ID = COM.COMPANY_ID
	ORDER BY C.CREATED_AT DESC LIMIT 3;
END


CREATE PROCEDURE INSERT_USER (IN V_USER_NAME VARCHAR(255), IN V_USER_EMAIL VARCHAR(255), IN V_USER_PASSWORD VARCHAR(255), IN V_IMG_URL VARCHAR(255))
BEGIN
	INSERT INTO USER 
	(USER_ID, USER_NAME, USER_EMAIL, USER_PASSWORD, TYPE_ID, IMG_URL) 
	VALUES (UUID(), 'V_USER_NAME', 'V_USER_EMAIL', 'V_USER_PASSWORD', '50821e47-0be6-11f0-b0d3-020017000d59', 'V_IMG_URL'); 
END


CALL INSERT_USER('TESTE','T@T','PASS','IMG');

