/* 
####################################
####### CALC AVG #######################################################################################################################################################################
####################################
*/

-- RANK DA EMPRESA
SELECT COMPANY_RANK FROM COMPANY WHERE COMPANY_ID = '${COMPANY_ID}';

UPDATE COMPANY 
SET COMPANY_RANK = (SELECT AVG(COMMENT_RANK) FROM COMMENT WHERE COMPANY_ID = '${COMPANY_ID}')
WHERE COMPANY_ID = '${COMPANY_ID}'
;

-- RANK DO PRODUTO
SELECT PRODUCT_RANK FROM PRODUCT WHERE PRODUCT_ID = '${PRODUCT_ID}';

UPDATE PRODUCT 
SET PRODUCT_RANK = (SELECT AVG(COMMENT_RANK) FROM COMMENT WHERE PRODUCT_ID = '${PRODUCT_ID}')
WHERE PRODUCT_ID = '${PRODUCT_ID}'
;

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