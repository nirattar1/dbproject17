CREATE TABLE IF NOT EXISTS city 
  ( 
     id         INT(6) NOT NULL auto_increment PRIMARY KEY, 
     name       VARCHAR(30) NOT NULL, 
     north_lat  DECIMAL(50, 7) NOT NULL, 
     south_lat  DECIMAL(50, 7) NOT NULL, 
     east_lon   DECIMAL(50, 7) NOT NULL, 
     west_lon   DECIMAL(50, 7) NOT NULL, 
     created_at TIMESTAMP 
  ) 
;

CREATE TABLE IF NOT EXISTS category 
  ( 
     id         VARCHAR(32) PRIMARY KEY, 
     name       VARCHAR(32) NOT NULL, 
     created_at TIMESTAMP 
  ) 
;

CREATE TABLE IF NOT EXISTS restaurant 
  ( 
     id            VARCHAR(32) PRIMARY KEY, 
     name          VARCHAR(256) NOT NULL, 
     city_id       INT(6) NOT NULL, 
          FOREIGN KEY(city_id) REFERENCES city(id), 
          url           VARCHAR(256), 
          has_menu      INT(1), 
          phone         VARCHAR(32), 
          address       VARCHAR(50), 
          category_id   VARCHAR(32) NOT NULL, 
          FOREIGN KEY(category_id) REFERENCES category(id), 
     checkinscount INT(24), 
     userscount    INT(24), 
     tipcount      INT(24), 
     created_at    TIMESTAMP 
  ) 
;

CREATE TABLE IF NOT EXISTS dish 
  ( 
     id            INT(16) NOT NULL auto_increment PRIMARY KEY, 
     restaurant_id VARCHAR(32) NOT NULL, 
          FOREIGN KEY(restaurant_id) REFERENCES restaurant(id), 
     section_name  VARCHAR(256), 
     name          VARCHAR(256), 
     description   VARCHAR(256), 
     price         DECIMAL(8, 2), 
     created_at    TIMESTAMP 
  ) 
engine=myisam
;

CREATE TABLE IF NOT EXISTS openhours 
  ( 
     restaurant_id VARCHAR(32), 
          FOREIGN KEY(restaurant_id) REFERENCES restaurant(id), 
     day           INT(1), 
     open_hour     TIME, 
     close_hour    TIME, 
     created_at    TIMESTAMP 
  ) 
; 

CREATE TABLE IF NOT EXISTS categorymain 
  ( 
     category_id VARCHAR(32) PRIMARY KEY, 
          FOREIGN KEY(category_id) REFERENCES category(id), 
          main_id     VARCHAR(32), 
          FOREIGN KEY(main_id) REFERENCES category(id), 
     created_at  TIMESTAMP 
  ) 
;