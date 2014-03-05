
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

INSERT INTO MEMBER(username,password,picture) VALUES ("Elytio","dHVsb3JhcGE=","nothing");

CREATE TABLE COMMENT(
idComment INTEGER PRIMARY KEY AUTO_INCREMENT ,
placeMark FLOAT,
date DATE,
text VARCHAR(500),
idMember INTEGER,
FOREIGN KEY (idMember) REFERENCES MEMBER(idMember)
);

CREATE TABLE PLACE(
idPlace INTEGER PRIMARY KEY AUTO_INCREMENT ,
name VARCHAR(100) NOT NULL UNIQUE,
summary VARCHAR(1000),
address VARCHAR(500),
idMember INTEGER,
FOREIGN KEY (idMember) REFERENCES MEMBER(idMember)
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
availibility BOOLEAN,
free BOOLEAN,
idFacility INTEGER,
idPlace INTEGER,
FOREIGN KEY (idPlace) REFERENCES PLACE(idPlace),
FOREIGN KEY (idFacility) REFERENCES FACILITY(idFacility)
);













