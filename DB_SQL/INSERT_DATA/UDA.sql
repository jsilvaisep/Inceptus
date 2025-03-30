select * from UDA;
DELETE from UDA ;

INSERT INTO UDA (UDA_ID, PRODUCT_ID, UDA_VALUES)
VALUES
(UUID(), 'fefecd40-0bf4-11f0-b0d3-020017000d59', '{
"motor" : {"potencia": "500w", "rpm": "20"}, 
"grill" : {"potencia": "200w"}, 
"micro-ondas" : {"pot_min" : "200w", "pot_max" : "3000w"}
}');

SELECT u.UDA_VALUES  FROM UDA u ;
select u.UDA_VALUES->>'$.motor.potencia' from UDA u 
;
