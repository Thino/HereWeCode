
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
idMember INTEGER,
FOREIGN KEY (idMember) REFERENCES MEMBER(idMember)
);


CREATE TABLE COMMENT(
idComment INTEGER PRIMARY KEY AUTO_INCREMENT ,
placeMark FLOAT,
date DATE,
text VARCHAR(500),
idMember INTEGER,
idPlace INTEGER,
FOREIGN KEY (idMember) REFERENCES MEMBER(idMember),
FOREIGN KEY (idPlace) REFERENCES PLACE(idPlace)
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
FOREIGN KEY (idComment) REFERENCES COMMENT(idComment),
FOREIGN KEY (idFacility) REFERENCES FACILITY(idFacility)
);

CREATE TABLE CRITERIA(
idCriteria INTEGER PRIMARY KEY AUTO_INCREMENT ,
availability BOOLEAN,
free BOOLEAN,
idFacility INTEGER,
idPlace INTEGER,
FOREIGN KEY (idPlace) REFERENCES PLACE(idPlace),
FOREIGN KEY (idFacility) REFERENCES FACILITY(idFacility)
);

INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Wifi','http://78.124.149.171/HereWeCode/img/NoWifi.jpg','http://78.124.149.171/HereWeCode/img/RedWifi.jpg','http://78.124.149.171/HereWeCode/img/OrangeWifi.jpg','http://78.124.149.171/HereWeCode/img/GreenWifi.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Comfort','http://78.124.149.171/HereWeCode/img/NoChair.jpg','http://78.124.149.171/HereWeCode/img/RedChair.jpg','http://78.124.149.171/HereWeCode/img/OrangeChair.jpg','http://78.124.149.171/HereWeCode/img/GreenChair.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Coffee','http://78.124.149.171/HereWeCode/img/NoCup.jpg','http://78.124.149.171/HereWeCode/img/RedCup.jpg','http://78.124.149.171/HereWeCode/img/OrangeCup.jpg','http://78.124.149.171/HereWeCode/img/GreenCup.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Plugs','http://78.124.149.171/HereWeCode/img/NoPlug.jpg','http://78.124.149.171/HereWeCode/img/RedPlug.jpg','http://78.124.149.171/HereWeCode/img/OrangePlug.jpg','http://78.124.149.171/HereWeCode/img/GreenPlug.jpg');
INSERT INTO FACILITY(name,iconNoItem,iconRed,iconOrange,iconGreen) VALUES ('Desks','http://78.124.149.171/HereWeCode/img/NoDesk.jpg','http://78.124.149.171/HereWeCode/img/RedDesk.jpg','http://78.124.149.171/HereWeCode/img/OrangeDesk.jpg','http://78.124.149.171/HereWeCode/img/GreenDesk.jpg');

INSERT INTO PLACE(name,summary,address,approved,idMember) VALUES ('Mc Donalds','Un bon vieux Macdo situé à la Pardieu à Clermont-Ferrand. Tout neuf et assez sympa !','La pardieu 63000 Clermont-Ferrand', true, 1 );
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','1','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','true','2','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('true','false','3','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('false','false','4','1');
INSERT INTO CRITERIA(availability,free,idFacility,idPlace) VALUES ('false','false','5','1');

INSERT INTO COMMENT(date,text,idMember,idPlace) VALUES ('2013-12-31 23:59:59','Je kiffe le MacDo =D',1,1);




