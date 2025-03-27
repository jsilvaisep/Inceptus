select COUNT(*) from CATEGORY c;
select COUNT(*) from COMMENT c ;
select COUNT(*) from COMPANY c ;
select COUNT(*) from PRODUCT p ;
select COUNT(*) from USER u ;




Create Procedure COMPANY_TOP() 
BEGIN
	select * from COMPANY c order by c.COMPANY_RANK desc limit 3;
END


CREATE PROCEDURE PRODUCT_TOP()
BEGIN
select p.PRODUCT_NAME, p.PRODUCT_DESCRIPTION, p.IMG_URL  from PRODUCT p ORDER BY p.PRODUCT_RANK DESC LIMIT 3;
END

CREATE PROCEDURE TRENDING_TOP()
	BEGIN
		SELECT c.COMMENT_TEXT, com.COMPANY_NAME 
		FROM COMMENT c
		INNER JOIN COMPANY com ON c.COMPANY_ID = com.COMPANY_ID
		ORDER BY c.CREATED_AT DESC LIMIT 3;
	END

	
DROP Procedure COMPANY_TOP;
DROP PROCEDURE PRODUCT_TOP;
DROP PROCEDURE TRENDING_TOP;

call company_top();