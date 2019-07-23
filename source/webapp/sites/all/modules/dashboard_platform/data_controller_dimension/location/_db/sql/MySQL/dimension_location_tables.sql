CREATE TABLE states (
       state_id             INTEGER NOT NULL,
       code                 CHAR(2) NOT NULL,
       name                 VARCHAR(100) NOT NULL,
       fips                 CHAR(2) NOT NULL,
       CONSTRAINT pk_states PRIMARY KEY (state_id),
       CONSTRAINT uk_states_code UNIQUE (code)
)
ENGINE = InnoDB;


CREATE TABLE congressional_districts (
       congressional_district_id INTEGER NOT NULL,
       code                 CHAR(2) NOT NULL,
       state_id             INTEGER NOT NULL,
       CONSTRAINT pk_congressional_districts PRIMARY KEY (congressional_district_id), 
       CONSTRAINT fk_congress_districts_states FOREIGN KEY (state_id) REFERENCES states (state_id),
       CONSTRAINT uk_congressional_districts UNIQUE (state_id, code)
)
ENGINE = InnoDB;


CREATE TABLE counties (
       county_id            INTEGER NOT NULL,
       name                 VARCHAR(100) NOT NULL,
       fips                 CHAR(3) NOT NULL,
       state_id             INTEGER NOT NULL,
       CONSTRAINT pk_counties PRIMARY KEY (county_id), 
       CONSTRAINT fk_counties_states FOREIGN KEY (state_id) REFERENCES states (state_id),
       CONSTRAINT uk_counties_name UNIQUE (state_id, name),
       CONSTRAINT uk_counties_fips UNIQUE (state_id, fips)
)
ENGINE = InnoDB;


CREATE TABLE cities (
       city_id              INTEGER NOT NULL,
       name                 VARCHAR(100) NOT NULL,
       fips                 CHAR(5) NOT NULL,
       state_id             INTEGER NOT NULL,
       parent_city_id       INTEGER NULL,
       CONSTRAINT pk_cities PRIMARY KEY (city_id), 
       CONSTRAINT fk_cities_states FOREIGN KEY (state_id) REFERENCES states (state_id), 
       CONSTRAINT fk_cities_cities_parent FOREIGN KEY (parent_city_id) REFERENCES cities (city_id),
       CONSTRAINT uk_cities UNIQUE (state_id, name)
)
ENGINE = InnoDB;


CREATE TABLE zips (
       zip_id               INTEGER NOT NULL,
       zip                  CHAR(5) NOT NULL,
       state_id             INTEGER NOT NULL,
       CONSTRAINT pk_zips PRIMARY KEY (zip_id),
       CONSTRAINT fk_zips_states FOREIGN KEY (state_id) REFERENCES states (state_id),
       CONSTRAINT uk_zips UNIQUE (zip)
)
ENGINE = InnoDB;

CREATE INDEX indx_zips_state ON zips(state_id);


CREATE TABLE zip4s (
       zip_id                    INTEGER NOT NULL,
       zip4                      CHAR(4) NOT NULL,
       city_id                   INTEGER NOT NULL,
       county_id                 INTEGER NOT NULL,
       congressional_district_id INTEGER NOT NULL, 
       CONSTRAINT fk_zip4s_counties FOREIGN KEY (county_id) REFERENCES counties (county_id), 
       CONSTRAINT fk_zip4s_cities FOREIGN KEY (city_id) REFERENCES cities (city_id), 
       CONSTRAINT fk_zip4s_zips FOREIGN KEY (zip_id) REFERENCES zips (zip_id), 
       CONSTRAINT fk_zip4s_congress_districts FOREIGN KEY (congressional_district_id) REFERENCES congressional_districts (congressional_district_id),
       CONSTRAINT uk_zip4s UNIQUE (zip_id, zip4)
)
ENGINE = InnoDB;

CREATE INDEX indx_zip4s_city ON zip4s(city_id);
CREATE INDEX indx_zip4s_county ON zip4s(county_id);
CREATE INDEX indx_zip4s_congress_district ON zip4s(congressional_district_id);


CREATE TABLE region_def (
       region_def_id        INTEGER NOT NULL,
       code                 VARCHAR(20) NOT NULL,
       name                 VARCHAR(100) NOT NULL,
       CONSTRAINT pk_region_def PRIMARY KEY (region_def_id),
       CONSTRAINT uk_region_def UNIQUE (code)
)
ENGINE = InnoDB;


CREATE TABLE regions (
       region_id            INTEGER NOT NULL,
       code                 VARCHAR(4) NOT NULL,
       name                 VARCHAR(100) NOT NULL,
       region_def_id        INTEGER NOT NULL,
       parent_region_id     INTEGER NULL,
       CONSTRAINT pk_regions PRIMARY KEY (region_id), 
       CONSTRAINT fk_regions_parent FOREIGN KEY (parent_region_id) REFERENCES regions (region_id), 
       CONSTRAINT fk_regions_region_def FOREIGN KEY (region_def_id) REFERENCES region_def (region_def_id),
       CONSTRAINT uk_regions UNIQUE (region_def_id, parent_region_id, code)
)
ENGINE = InnoDB;


CREATE TABLE region_states (
       region_id            INTEGER NOT NULL,
       state_id             INTEGER NOT NULL,
       CONSTRAINT pk_region_states PRIMARY KEY (region_id, state_id), 
       CONSTRAINT fk_region_states_regions FOREIGN KEY (region_id) REFERENCES regions (region_id), 
       CONSTRAINT fk_region_states_states FOREIGN KEY (state_id) REFERENCES states (state_id)
)
ENGINE = InnoDB;
