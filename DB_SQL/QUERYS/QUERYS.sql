SELECT * FROM ADDRESS a ;
SELECT * FROM ADMIN_POST;
SELECT * FROM CATEGORY;
SELECT * FROM COMMENT;
SELECT * FROM COMMENT_EXT ce ;
SELECT * FROM COMPANY;
SELECT * FROM POST;
SELECT * FROM POST_EXT pe ;
SELECT * FROM PRODUCT;
SELECT * FROM SEC_RST_CODE;
SELECT * FROM TAG;
SELECT * FROM USER;
SELECT * FROM U_TYPE ;
SELECT * FROM USER_TYPE;
SELECT * FROM C_TYPE ct ;

UPDATE COMPANY c SET COMPANY_STATUS = 'A';
UPDATE PRODUCT p SET PRODUCT_STATUS = 'A';

UPDATE COMPANY c SET COMPANY_STATUS = 'I';
UPDATE PRODUCT p SET PRODUCT_STATUS = 'I';

SELECT c.COMMENT_ID, c.COMMENT_TEXT, c.CREATED_AT, ce.COMMENT_EXT_TEXT, ce.CREATED_AT FROM COMMENT c 
INNER JOIN COMMENT_EXT ce ON ce.COMMENT_ID = c.COMMENT_ID
WHERE c.COMMENT_STATUS = 'A'
;

SELECT p.POST_ID , p.POST_CONTENT , p.CREATED_AT, pe.POST_EXT_CONTENT , pe.CREATED_AT FROM POST p 
INNER JOIN POST_EXT pe ON pe.POST_ID  = p.POST_ID 
WHERE p.POST_STATUS = 'A'
;

SELECT u.USER_EMAIL, u.USER_PASSWORD, ut2.USER_TYPE FROM USER u
INNER JOIN U_TYPE ut ON ut.USER_ID = u.USER_ID 
INNER JOIN USER_TYPE ut2 ON ut.TYPE_ID = ut2.TYPE_ID 
;

call insert_user ('teste', 'e@e.pt', 'password', '/img_path');



