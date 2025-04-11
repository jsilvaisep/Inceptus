SELECT * FROM ADDRESS a ;
SELECT * FROM ADMIN_POST;
SELECT * FROM CATEGORY;
SELECT * FROM COMMENT WHERE COMPANY_ID = '4d6d28d4-0be9-11f0-b0d3-020017000d59';
UPDATE COMMENT SET COMMENT_RANK = '5' WHERE PRODUCT_ID = 'fead11f6-0bf4-11f0-b0d3-020017000d59';
SELECT * FROM COMMENT_EXT ce ;
SELECT * FROM COMPANY WHERE COMPANY_ID = 'fea7f029-0bf4-11f0-b0d3-020017000d59';
UPDATE COMPANY SET COMPANY_RANK = 0;
SELECT * FROM POST;
SELECT * FROM POST_EXT pe ;
SELECT * FROM PRODUCT WHERE PRODUCT_STATUS = 'A' and PRODUCT_ID = 'feb834bf-1162-11f0-ab2e-020017000d59'; -- fefecd40-0bf4-11f0-b0d3-020017000d59
update PRODUCT set IMG_URL='/produtos/img3_1743777732.png' WHERE PRODUCT_STATUS = 'A' and PRODUCT_ID = 'feb834bf-1162-11f0-ab2e-020017000d59';
SELECT * FROM SEC_RST_CODE;
SELECT * FROM TAG;
SELECT * FROM USER;
SELECT * FROM USER_TYPE where TYPE_ID = '8986b070-100b-11f0-ab2e-020017000d59';
SELECT * FROM PROD_UDA pu ;
SELECT * FROM UDA;




CALL INSERT_COMPANY('USER_NAME_3','COMPANY_NOME_3', 'COMPANY_LOGIN_3','USER_EMAIL_3','COMPANY_PASSWORD_3','COMPANY_IMAGEM_3','COMPANY_EMAIL_3', 'COMPANY_SITE_3'); 

CALL INSERT_USER('USER_NOME', 'USER_LOGIN','USER_EMAIL','USER_PASSWORD','USER_IMAGEM');

ALTER TABLE POST_EXT MODIFY COLUMN USER_ID BINARY(36) AFTER POST_ID;

SELECT u.USER_ID, c.COMPANY_ID, u.USER_LOGIN, u.USER_NAME, c.COMPANY_NAME, u.USER_EMAIL, c.COMPANY_EMAIL, c.COMPANY_SITE FROM USER u 
INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID 
WHERE c.COMPANY_EMAIL = 'COMPANY_EMAIL_3';

empresas/img3.png
CALL DELETE_PRODUCT('fe6cf63c-0bf4-11f0-b0d3-020017000d59');

-- /../produtos/img1_1743777732.png;/../produtos/img2_1743777732.png;/../produtos/img3_1743777732.png
SELECT * FROM PRODUCT p 
INNER JOIN COMMENT c ON c.PRODUCT_ID = p.PRODUCT_ID 
INNER JOIN COMMENT_EXT ce ON ce.COMMENT_ID = c.COMMENT_ID 
WHERE p.PRODUCT_ID = 'fe73532d-0bf4-11f0-b0d3-020017000d59' 
AND p.PRODUCT_STATUS = 'A'
;

UPDATE PRODUCT c SET c.PRODUCT_NAME = 'Testar trigger de update' where PRODUCT_ID = 'fe73532d-0bf4-11f0-b0d3-020017000d59';

SELECT pe.POST_EXT_CONTENT, u.USER_NAME
FROM POST_EXT pe
INNER JOIN POST p ON p.POST_ID = pe.POST_ID
INNER JOIN USER u ON u.USER_ID = pe.USER_ID
WHERE p.POST_STATUS = 'A'
ORDER BY pe.CREATED_AT DESC;


SELECT * FROM COMMENT c WHERE c.PRODUCT_ID = 'fefecd40-0bf4-11f0-b0d3-020017000d59';



SELECT u.USER_ID, c.COMPANY_ID
                       FROM USER u 
                       INNER JOIN COMPANY c ON c.USER_ID = u.USER_ID
                       WHERE u.USER_ID ='a60585b7-11a3-11f0-ab2e-020017000d59'
                       
SELECT * FROM PRODUCT p WHERE COMPANY_ID = 'a6059147-11a3-11f0-ab2e-020017000d59';



SELECT * FROM CATEGORY c ;
SELECT * FROM UDA u ;
SELECT PRODUCT_ID, PRODUCT_NAME, PRODUCT_STATUS FROM PRODUCT p where PRODUCT_ID = '284c8d37-147b-11f0-89dc-020017000d59';
SELECT * FROM PROD_UDA pu ;

SELECT p.PRODUCT_ID, p.PRODUCT_NAME, c.CATEGORY_NAME, c.CATEGORY_NAME , u.UDA_VALUES FROM PRODUCT p 
INNER JOIN PROD_UDA pu ON pu.PRODUCT_ID = p.PRODUCT_ID 
INNER JOIN CATEGORY c ON c.CATEGORY_ID = p.CATEGORY_ID
INNER JOIN UDA u ON u.UDA_ID = pu.UDA_ID 
WHERE c.CATEGORY_NAME = u.UDA_VALUES->>'$.CATEGORIA'
AND c.CATEGORY_NAME = '';



CALL DELETE_PRODUCT('07661b9a-0bf5-11f0-b0d3-020017000d59');

SELECT 
    p.PRODUCT_ID, 
    p.PRODUCT_NAME, 
    c.CATEGORY_NAME, 
    JSON_UNQUOTE(JSON_EXTRACT(u.UDA_VALUES, '$.CATEGORIA')) AS SPECIFICACOES 
FROM PRODUCT p 
INNER JOIN PROD_UDA pu ON pu.PRODUCT_ID = p.PRODUCT_ID 
INNER JOIN CATEGORY c ON c.CATEGORY_ID = p.CATEGORY_ID 
INNER JOIN UDA u ON u.UDA_ID = pu.UDA_ID 
WHERE JSON_UNQUOTE(JSON_EXTRACT(u.UDA_VALUES, '$.CATEGORIA')) = c.CATEGORY_NAME 
  AND c.CATEGORY_NAME = :nome_categoria;

CALL INSERT_POST (:company_id, :title, :subtitle, :post_content);

        SELECT p.*, c.COMPANY_NAME 
        FROM POST p 
        INNER JOIN COMPANY c ON c.COMPANY_ID = p.COMPANY_ID
        WHERE c.COMPANY_ID = '56c0cdde-1557-11f0-89dc-020017000d59'
          AND p.POST_STATUS = 'A'
        ORDER BY p.CREATED_AT DESC
        
        SELECT * FROM POST p WHERE p.COMPANY_ID = 'a6059147-11a3-11f0-ab2e-020017000d59';
        
                SELECT p.POST_ID, p.POST_CONTENT, p.POST_STATUS, c.COMPANY_NAME, c.COMPANY_ID
        FROM POST p
        INNER JOIN COMPANY c ON p.COMPANY_ID = c.COMPANY_ID
        INNER JOIN USER u ON u.USER_ID = c.USER_ID 
        WHERE u.USER_ID = 'a60585b7-11a3-11f0-ab2e-020017000d59'
        ORDER BY p.UPDATED_AT DESC;
                
        SELECT * FROM PRODUCT p ;
        
          SELECT * FROM POST order by POST_STATUS asc, UPDATED_AT desc;