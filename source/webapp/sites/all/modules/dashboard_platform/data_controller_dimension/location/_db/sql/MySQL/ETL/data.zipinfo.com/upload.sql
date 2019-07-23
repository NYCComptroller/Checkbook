CREATE SCHEMA zip4s;

USE zip4s;

-- *****************************************************************************
--   ZIP+4
-- *****************************************************************************
SELECT 'Importing ZIP+4 ...' AS ' ';

CREATE TABLE zip4_source (
    zip                     CHAR(5) NOT NULL,
    zip4                    CHAR(4) NOT NULL,
    city_name               VARCHAR(28) NOT NULL,
    city_fips               CHAR(5) NOT NULL,
    county_name             VARCHAR(25) NOT NULL,
    county_fips             CHAR(3) NOT NULL,
    state_code              CHAR(2) NOT NULL,
    state_fips              CHAR(2) NOT NULL,
    state_id                INTEGER NULL,
    congressional_district  CHAR(2) NOT NULL
)
ENGINE = MyISAM;


TRUNCATE TABLE zip4_source;

LOAD DATA INFILE '/usr/tmp/zip4_source.csv'
IGNORE
INTO TABLE zip4_source
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\r\n'
(zip, zip4, city_name, city_fips, county_name, county_fips, state_code, state_fips, congressional_district);

SHOW WARNINGS;


CREATE INDEX indx_zip4_source_state_code ON zip4_source(state_code);

SELECT 'Completed importing ZIP+4.' AS ' ';


-- *****************************************************************************
--   Congressional Districts
-- *****************************************************************************
SELECT 'Importing Congressional Districts ...' AS ' ';

CREATE TABLE congressional_district_source (
       state_code           CHAR(2) NOT NULL,
       state_fips           CHAR(2) NOT NULL,
       district_code        CHAR(2) NOT NULL,
       first_name           VARCHAR(20) NOT NULL,
       last_name            VARCHAR(20) NOT NULL,
       party_affiliation    CHAR NOT NULL,
       address              VARCHAR(100) NOT NULL,
       telephone            CHAR(12) NOT NULL,
       fax                  CHAR(12) NOT NULL,
       email                VARCHAR(100) NULL
)
ENGINE = MyISAM;


TRUNCATE TABLE congressional_district_source;

LOAD DATA INFILE '/usr/tmp/congressional_district_source.csv'
IGNORE
INTO TABLE congressional_district_source
FIELDS ENCLOSED BY '"' TERMINATED BY ','
LINES TERMINATED BY '\r\n'
IGNORE 1 LINES;

SHOW WARNINGS;

SELECT 'Completed importing Congressional Districts.' AS ' ';
