select * from UDA;
DELETE from UDA ;

INSERT INTO UDA (UDA_ID, UDA_VALUES)
VALUES
(UUID(), '{
"CATEGORIA" : "DRONES",
"motor" : 
	{"potencia": "500w",
	"rpm": "20"}
}');

INSERT INTO UDA (UDA_ID, UDA_VALUES)
VALUES
(UUID(), '{
"CATEGORIA" : "SOFTWARE",
"frontend" : "Angular" , 
"backend" : ".NET" 
}');

INSERT INTO UDA (UDA_ID, UDA_VALUES)
VALUES
(UUID(), '{
"CATEGORIA" : "MICRO-ONDAS",
"grill" : {"potencia": "200w"}, 
"micro-ondas" : {"pot_min" : "200w", "pot_max" : "3000w"}
}');

SELECT * FROM UDA u where u.UDA_VALUES->>'$.CATEGORIA' = 'DRONES';

select u.UDA_VALUES->>'$.motor' from UDA u where u.UDA_VALUES->>'$.CATEGORIA' = 'DRONES';  
;
