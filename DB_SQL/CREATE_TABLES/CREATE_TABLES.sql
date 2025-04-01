/*
 * TODO - 3FN
 * Criar tabela U_TYPE para ligar USER a USER_TYPES
 * Criar tabela UDA_PRODUCT e UDA_COMPANY para ligar UDA com PRODUCT e COMPANY
 * Criar tabela CAT_TYPES para ligar CATEGORY com CATEGORY_TYPES
*/

/* 
###############################
####### TABLE USER_TYPE #####################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.USER_TYPE (
	TYPE_ID BINARY(36) NOT NULL,
	USER_TYPE VARCHAR(100) NOT NULL,
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	
	CONSTRAINT TYPE__PK PRIMARY KEY (TYPE_ID),
	CONSTRAINT TYPE_UNIQUE UNIQUE KEY (USER_TYPE)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing user type data';

/* 
###############################
####### TABLE U_TYPE #####################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.U_TYPE (
	TYPE_ID BINARY(36) NOT NULL,
	USER_ID BINARY(36) NOT NULL,
	CONSTRAINT U_TYPE_PK PRIMARY KEY (TYPE_ID,USER_ID),
	CONSTRAINT U_TYPE_TYPE_ID_FK FOREIGN KEY (TYPE_ID) REFERENCES DB_INCEPTUS_PP.USER_TYPE(TYPE_ID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT U_TYPE_USER_ID_FK FOREIGN KEY (USER_ID) REFERENCES DB_INCEPTUS_PP.USER(USER_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Connection Table for user to user type';

/* 
###############################
####### TABLE USER ##########################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.USER (
	USER_ID BINARY(36) NOT NULL,
	USER_NAME VARCHAR(100) NOT NULL,
	USER_EMAIL VARCHAR(100) NOT NULL,
	USER_PASSWORD VARCHAR(100) DEFAULT 'Mudar#1234' NOT NULL,
	USER_STATUS VARCHAR(1) DEFAULT 'A' NOT NULL COMMENT 'Values (A)ctive, (I)nactive',
	--- TYPE_ID BINARY(36) NOT NULL,
	IMG_URL VARCHAR(100),
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	
	CONSTRAINT USER_PK PRIMARY KEY (USER_ID),
	CONSTRAINT USER_STATUS_CK CHECK (USER_STATUS = 'A' OR USER_STATUS = 'I'),
	CONSTRAINT USER_UNIQUE UNIQUE KEY (USER_EMAIL),
	-- CONSTRAINT USER_TYPE_ID_FK FOREIGN KEY (TYPE_ID) REFERENCES DB_INCEPTUS_PP.U_TYPE(TYPE_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing user data';


/* 
###############################
####### TABLE COMPANY #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.COMPANY (
	COMPANY_ID BINARY(36) NOT NULL,
	COMPANY_NAME VARCHAR(100) NOT NULL,
	COMPANY_DESCRIPTION VARCHAR(1000) NOT NULL,
	COMPANY_EMAIL VARCHAR(100) NOT NULL,
	COMPANY_PASSWORD VARCHAR(100) DEFAULT 'Mudar#1234' NOT NULL,
	COMPANY_SITE VARCHAR(100) NOT NULL,
	COMPANY_STATUS VARCHAR(1) DEFAULT 'A' NOT NULL,
	COMPANY_RANK DECIMAL(2,1) DEFAULT 0,
	COMPANY_VIEW_QTY INT,
	-- TYPE_ID BINARY(36) DEFAULT 2 NOT NULL,
	IMG_URL VARCHAR(100),
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	
	CONSTRAINT COMPANY_PK PRIMARY KEY (COMPANY_ID),
	CONSTRAINT COMPANY_STATUS_CK CHECK (COMPANY_STATUS = 'A' OR COMPANY_STATUS = 'I'),
	CONSTRAINT COMPANY_RANK_CK CHECK (COMPANY_RANK >= 0.0 OR COMPANY_RANK <= 5.0),
	CONSTRAINT COMPANY_UNIQUE UNIQUE KEY (COMPANY_EMAIL)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing company data';

/* 
###############################
####### TABLE C_TYPE #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.C_TYPE (
	TYPE_ID BINARY(36) NOT NULL,
	COMPANY_ID BINARY(36) NOT NULL,
	CONSTRAINT C_TYPE_PK PRIMARY KEY (TYPE_ID,COMPANY_ID),
	CONSTRAINT C_TYPE_TYPE_ID_FK FOREIGN KEY (TYPE_ID) REFERENCES DB_INCEPTUS_PP.USER_TYPE(TYPE_ID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT C_TYPE_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Connection Table for company to user type';

/* 
###############################
####### TABLE ADDRESS #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.ADDRESS (
	ADDRESS_ID BINARY(36) NOT NULL,
	COMPANY_ID BINARY(36) NOT NULL,
	ADDRESS VARCHAR(255) NOT NULL,
	PHONE CHAR(9),
	MOBILE CHAR(9),
	ZIP CHAR(8),
	CITY VARCHAR(255) NOT NULL,
	CONSTRAINT ADDRESS_PK PRIMARY KEY (ADDRESS_ID,COMPANY_ID),
	CONSTRAINT ADDRESS_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT ADDRESS_PHONE_CK CHECK (PHONE REGEXP '^[0-9]{9}$' OR PHONE IS NULL),
    CONSTRAINT ADDRESS_MOBILE_CK CHECK (MOBILE REGEXP '^[0-9]{9}$' OR MOBILE IS NULL),
    CONSTRAINT ADDRESS_ZIP_CK CHECK (ZIP REGEXP '^[0-9]{4}-[0-9]{3}$' OR ZIP IS NULL)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Connection Table for company address type';

/* 
###############################
####### TABLE CATEGORY ######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.CATEGORY (
	CATEGORY_ID BINARY(36) NOT NULL,
	CATEGORY_NAME VARCHAR(100) NOT NULL,
	CATEGORY_TYPE VARCHAR(100) NOT NULL,
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	
	CONSTRAINT CATEGORY_PK PRIMARY KEY (CATEGORY_ID),
	CONSTRAINT CATEGORY_UNIQUE UNIQUE KEY (CATEGORY_NAME)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing category data';

/* 
###############################
####### TABLE PRODUCT #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.PRODUCT (
	PRODUCT_ID BINARY(36) NOT NULL,
	PRODUCT_NAME VARCHAR(100) NOT NULL,
	PRODUCT_DESCRIPTION VARCHAR(1000) NOT NULL,
	CATEGORY_ID BINARY(36) NOT NULL,
	COMPANY_ID BINARY(36) NOT NULL,
	PRODUCT_STATUS VARCHAR(1) DEFAULT 'A' NOT NULL,
	PRODUCT_RANK DECIMAL(2,1) DEFAULT 0,
	PRODUCT_VIEW_QTY INT,
	IMG_URL VARCHAR(100),
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,	
	CONSTRAINT PRODUCT_PK PRIMARY KEY (PRODUCT_ID),
	CONSTRAINT PRODUCT_UNIQUE UNIQUE KEY (PRODUCT_NAME, COMPANY_ID),
	CONSTRAINT PRODUCT_STATUS_CK CHECK (PRODUCT_STATUS = 'A' OR PRODUCT_STATUS = 'I'),
	CONSTRAINT PRODUCT_RANK_CK CHECK (PRODUCT_RANK >= 0.0 OR PRODUCT_RANK <= 5.0),
	CONSTRAINT PRODUCT_CATEGORY_ID_FK FOREIGN KEY (CATEGORY_ID) REFERENCES DB_INCEPTUS_PP.CATEGORY(CATEGORY_ID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT PRODUCT_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing prodcut data';

/* 
###############################
####### TABLE COMMENT #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.COMMENT (
	COMMENT_ID BINARY(36) NOT NULL,
	USER_ID BINARY(36) NOT NULL,
	COMPANY_ID BINARY(36) NOT NULL,
	PRODUCT_ID BINARY(36) NOT NULL,
	COMMENT_TEXT VARCHAR(2000) NOT NULL,
	COMMENT_RANK INT NOT NULL DEFAULT 0,
	COMMENT_STATUS VARCHAR(1) DEFAULT 'A' NOT NULL,
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,		
	CONSTRAINT COMMENT_PK PRIMARY KEY (COMMENT_ID),
	CONSTRAINT COMMENT_STATUS_CK CHECK (COMMENT_STATUS = 'A' OR COMMENT_STATUS = 'I'),
	CONSTRAINT COMMENT_RANK_CK CHECK (COMMENT_RANK >= 0.0 OR COMMENT_RANK <= 5.0),
	CONSTRAINT COMMENT_USER_ID_FK FOREIGN KEY (USER_ID) REFERENCES DB_INCEPTUS_PP.USER(USER_ID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT COMMENT_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT COMMENT_PRODUCT_ID_FK FOREIGN KEY (PRODUCT_ID) REFERENCES DB_INCEPTUS_PP.PRODUCT(PRODUCT_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing comment data';

/* 
###############################
####### TABLE COMMENT_EXT #######################################################################################################################################################################################
###############################
*/

CREATE TABLE DB_INCEPTUS_PP.COMMENT_EXT (
	COMMENT_EXT_ID BINARY(36) NOT NULL,
	COMMENT_ID BINARY(36) NOT NULL,
	COMMENT_EXT_TEXT VARCHAR(2000) NOT NULL,
	CREATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	UPDATED_AT DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,		
	CONSTRAINT COMMENT_EXT_PK PRIMARY KEY (COMMENT_EXT_ID),
	CONSTRAINT COMMENT_EXT_COMMENT_ID_FK FOREIGN KEY (COMMENT_ID) REFERENCES DB_INCEPTUS_PP.COMMENT(COMMENT_ID) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing comment response data';

/* 
###############################
####### TABLE POST ##########################################################################################################################################################################################
###############################
*/

CREATE TABLE POST (
  	POST_ID BINARY(36) NOT NULL,
  	COMPANY_ID BINARY(36) NOT NULL,
  	POST_CONTENT VARCHAR(2000) NOT NULL,
  	POST_STATUS VARCHAR(1) DEFAULT 'A',
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
  	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (POST_ID),
	CONSTRAINT POST_STATUS_CK CHECK (POST_STATUS = 'A' OR POST_STATUS = 'I'),
  	CONSTRAINT POST_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY (COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing posts data';

/* 
###############################
####### TABLE POST_EXT ##########################################################################################################################################################################################
###############################
*/

CREATE TABLE POST_EXT (
  	POST_EXT_ID BINARY(36) NOT NULL,
  	POST_ID BINARY(36) NOT NULL,
  	POST_EXT_CONTENT VARCHAR(2000) NOT NULL,
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
  	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (POST_EXT_ID),
  	CONSTRAINT POST_EXT_POST_ID_FK FOREIGN KEY (POST_ID) REFERENCES DB_INCEPTUS_PP.POST (POST_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing posts response data';

/* 
###############################
####### TABLE TAG ###########################################################################################################################################################################################
###############################
*/

CREATE TABLE TAG (
  	TAG_ID BINARY(36) NOT NULL,
  	TAG_NAME VARCHAR(50) NOT NULL,
  	TAG_STATUS VARCHAR(1) DEFAULT 'A',
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
  	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (TAG_ID),
	CONSTRAINT TAG_STATUS_CK CHECK (TAG_STATUS = 'A' OR TAG_STATUS = 'I')
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing tags data';

/* 
###############################
####### TABLE TAG_COMPANY #####################################################################################################################################################################################
###############################
*/

CREATE TABLE TAG_COMPANY (
  	TAG_ID BINARY(36) NOT NULL,
  	COMPANY_ID BINARY(36) NOT NULL,
  	PRIMARY KEY (TAG_ID, COMPANY_ID),
  	CONSTRAINT TAG_COMPANY_TAG_ID_FK FOREIGN KEY (TAG_ID) REFERENCES DB_INCEPTUS_PP.TAG (TAG_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT TAG_COMPANY_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing tags company data';

/* 
###############################
####### TABLE TAG_PRODCUT #####################################################################################################################################################################################
###############################
*/

CREATE TABLE TAG_PRODUCT (
  	TAG_ID BINARY(36) NOT NULL,
  	PRODUCT_ID BINARY(36) NOT NULL,
  	PRIMARY KEY (TAG_ID, PRODUCT_ID),
 	CONSTRAINT TAG_PRODUCT_TAG_ID_FK FOREIGN KEY (TAG_ID) REFERENCES DB_INCEPTUS_PP.TAG(TAG_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT TAG_PRODUCT_PRODCUT_ID_FK FOREIGN KEY (PRODUCT_ID) REFERENCES DB_INCEPTUS_PP.PRODUCT(PRODUCT_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing tags product data';

/* 
###############################
####### TABLE SEC_RST_CODE ##################################################################################################################################################################################
###############################
*/

CREATE TABLE SEC_RST_CODE (
  	SEC_RST_CODE_ID BINARY(36) NOT NULL,
 	USER_ID BINARY(36),
  	GENERATED_CODE INT NOT NULL,
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
  	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (SEC_RST_CODE_ID),
  	CONSTRAINT SEC_RST_CODE_ID_USER_ID_FK FOREIGN KEY (USER_ID) REFERENCES DB_INCEPTUS_PP.USER(USER_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing reset password volatile codes data';

/* 
###############################
####### TABLE ADMIN_POST ####################################################################################################################################################################################
###############################
*/

CREATE TABLE ADMIN_POST (
  	ADM_POST_ID BINARY(36) NOT NULL,
 	USER_ID BINARY(36),
  	COMPANY_ID BINARY(36),
  	ADM_POST_CONTENT VARCHAR(2000) NOT NULL,
  	ADM_POST_STATUS VARCHAR(1) DEFAULT 'A',
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
  	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (ADM_POST_ID),
	CONSTRAINT ADM_POST_STATUS_CK CHECK (ADM_POST_STATUS = 'A' OR ADM_POST_STATUS = 'I')
  	CONSTRAINT ADMIN_POST_USER_ID_FK FOREIGN KEY (USER_ID) REFERENCES DB_INCEPTUS_PP.USER(USER_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT ADMIN_POST_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY (COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing admin posts data';


/* 
###############################
####### TABLE UDA ####################################################################################################################################################################################
###############################
*/

CREATE TABLE UDA (
  	UDA_ID BINARY(36) NOT NULL,
  	UDA_VALUES JSON NOT NULL,
  	CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
 	UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  	PRIMARY KEY (UDA_ID)
)
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table containing admin posts data';

/* 
###############################
####### TABLE PROD_UDA ####################################################################################################################################################################################
###############################
*/

CREATE TABLE PROD_UDA (
  	UDA_ID BINARY(36) NOT NULL,
  	PRODUCT_ID BINARY(36) not null,
  	PRIMARY KEY (UDA_ID, PRODUCT_ID),
  	CONSTRAINT PROD_UDA_UDA_ID_FK FOREIGN KEY (UDA_ID) REFERENCES DB_INCEPTUS_PP.UDA (UDA_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT PROD_UDA_PRODUCT_ID_FK FOREIGN KEY (PRODUCT_ID) REFERENCES DB_INCEPTUS_PP.PRODUCT (PRODUCT_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table for link products to UDA data';

/* 
###############################
####### TABLE COMP_UDA ####################################################################################################################################################################################
###############################
*/

CREATE TABLE COMP_UDA (
  	UDA_ID BINARY(36) NOT NULL,
  	COMPANY_ID BINARY(36),
  	PRIMARY KEY (UDA_ID, COMPANY_ID),
  	CONSTRAINT COMP_UDA_UDA_ID_FK FOREIGN KEY (UDA_ID) REFERENCES DB_INCEPTUS_PP.UDA (UDA_ID) ON DELETE CASCADE ON UPDATE CASCADE,
  	CONSTRAINT COMP_UDA_COMPANY_ID_FK FOREIGN KEY (COMPANY_ID) REFERENCES DB_INCEPTUS_PP.COMPANY(COMPANY_ID) ON DELETE CASCADE ON UPDATE CASCADE
) 
ENGINE=InnoDB DEFAULT 
CHARSET=utf8mb4 
COLLATE=utf8mb4_0900_ai_ci
COMMENT='Table for link companies to UDA data';


