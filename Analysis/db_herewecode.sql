
SET NAMES UTF8;

DROP TABLE IF EXISTS CRITERIA;
DROP TABLE IF EXISTS MARK;
DROP TABLE IF EXISTS COMMENT;
DROP TABLE IF EXISTS PLACE;
DROP TABLE IF EXISTS FACILITY;
DROP TABLE IF EXISTS MEMBER;

CREATE TABLE MEMBER(
idMember INTEGER PRIMARY KEY AUTO_INCREMENT ,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(50) NOT NULL,
isAdmin BOOLEAN,
picture VARCHAR(200)
);

INSERT INTO MEMBER(username,password,picture,isAdmin) VALUES ("Elytio","dHVsb3JhcGE=","nothing",true);
INSERT INTO MEMBER(username,password,picture,isAdmin) VALUES ("Woute","eWVuYXBh","nothing",false);

CREATE TABLE PLACE(
idPlace INTEGER PRIMARY KEY AUTO_INCREMENT ,
name VARCHAR(100) NOT NULL UNIQUE,
summary VARCHAR(1000),
address VARCHAR(500),
approved BOOLEAN,
idMember INTEGER
);


CREATE TABLE COMMENT(
idComment INTEGER PRIMARY KEY AUTO_INCREMENT ,
placeMark FLOAT,
date DATE,
text VARCHAR(500),
idMember INTEGER,
idPlace INTEGER,
FOREIGN KEY (idMember) REFERENCES MEMBER(idMember) ON DELETE CASCADE ,
FOREIGN KEY (idPlace) REFERENCES PLACE(idPlace) ON DELETE CASCADE
);



CREATE TABLE FACILITY(
idFacility INTEGER PRIMARY KEY AUTO_INCREMENT ,
name VARCHAR(50) NOT NULL UNIQUE,
iconNoItem VARCHAR(200),
iconRed VARCHAR(200),
iconOrange VARCHAR(200),
iconGreen VARCHAR(200)
);


CREATE TABLE MARK(
idMark INTEGER PRIMARY KEY AUTO_INCREMENT ,
mark INTEGER,
idComment INTEGER,
idFacility INTEGER,
FOREIGN KEY (idComment) REFERENCES COMMENT(idComment) ON DELETE CASCADE,
FOREIGN KEY (idFacility) REFERENCES FACILITY(idFacility) ON DELETE CASCADE
);

CREATE TABLE CRITERIA(
idCriteria INTEGER PRIMARY KEY AUTO_INCREMENT,
availability BOOLEAN,
free BOOLEAN,
idFacility INTEGER,
idPlace INTEGER,
FOREIGN KEY (idPlace) REFERENCES PLACE(idPlace) ON DELETE CASCADE,
FOREIGN KEY (idFacility) REFERENCES FACILITY(idFacility) ON DELETE CASCADE
);

INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Wifi','http://78.124.135.14/HereWeCode/img/NoWifi.jpg','http://78.124.135.14/HereWeCode/img/RedWifi.jpg','http://78.124.135.14/HereWeCode/img/OrangeWifi.jpg','http://78.124.135.14/HereWeCode/img/GreenWifi.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Comfort','http://78.124.135.14/HereWeCode/img/NoChair.jpg','http://78.124.135.14/HereWeCode/img/RedChair.jpg','http://78.124.135.14/HereWeCode/img/OrangeChair.jpg','http://78.124.135.14/HereWeCode/img/GreenChair.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Coffee','http://78.124.135.14/HereWeCode/img/NoCup.jpg','http://78.124.135.14/HereWeCode/img/RedCup.jpg','http://78.124.135.14/HereWeCode/img/OrangeCup.jpg','http://78.124.135.14/HereWeCode/img/GreenCup.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Plugs','http://78.124.135.14/HereWeCode/img/NoPlug.jpg','http://78.124.135.14/HereWeCode/img/RedPlug.jpg','http://78.124.135.14/HereWeCode/img/OrangePlug.jpg','http://78.124.135.14/HereWeCode/img/GreenPlug.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Desks','http://78.124.135.14/HereWeCode/img/NoDesk.jpg','http://78.124.135.14/HereWeCode/img/RedDesk.jpg','http://78.124.135.14/HereWeCode/img/OrangeDesk.jpg','http://78.124.135.14/HereWeCode/img/GreenDesk.jpg');

INSERT INTO PLACE(name,summary,address,approved,idMember) VALUES ('Mc Donalds','Un bon vieux Macdo situé à la Pardieu à Clermont-Ferrand. Tout neuf et assez sympa !','La pardieu 63000 Clermont-Ferrand', true, 1 );
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','1','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','2','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','false','3','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('false','false','4','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('false','false','5','1');

INSERT INTO COMMENT(date,text,idMember,idPlace,placeMark) VALUES ('2013-12-31 23:59:59','Je kiffe le MacDo =D',1,1,4);
INSERT INTO COMMENT(date,text,idMember,idPlace,placeMark) VALUES ('2014-01-11 21:59:59','Moi aussi =)',2,1,5);

INSERT INTO PLACE(name,summary,address,approved,idMember) VALUES ('ISIMA','Ecole d\'ingé de Clermont ! Lieu spécial geeks !','Campus des Cézeaux 63000 Clermont-Ferrand', true, 2 );
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','1','2');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','2','2');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','false','3','2');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','4','2');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','5','2');

INSERT INTO COMMENT(date,text,idMember,idPlace,placeMark) VALUES ('2013-12-31 23:59:59','ISIMA is my school =D Just enjoy this place',1,2,4);
INSERT INTO COMMENT(date,text,idMember,idPlace,placeMark) VALUES ('2014-01-11 21:59:59','Wifi is free if you are a student !',2,2,3);


INSERT INTO MARK(mark,idComment,idFacility) VALUES (3,1,1);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,2,1);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,1,2);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (3,2,2);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,1,3);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,2,3);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (0,1,4);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (0,2,4);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (2,1,5);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (1,2,5);

INSERT INTO MARK(mark,idComment,idFacility) VALUES (5,3,1);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,4,1);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (3,3,2);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (3,4,2);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (3,3,3);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (2,4,3);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (5,3,4);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,4,4);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (4,3,5);
INSERT INTO MARK(mark,idComment,idFacility) VALUES (5,4,5);
