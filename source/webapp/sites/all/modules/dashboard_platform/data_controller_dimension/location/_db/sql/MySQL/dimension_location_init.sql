-- TRUNCATE TABLE congressional_districts;
-- TRUNCATE TABLE zips;
-- TRUNCATE TABLE cities;
-- TRUNCATE TABLE counties;
-- TRUNCATE TABLE zip4s;



UPDATE zip4s.zip4_source src
   SET src.state_id = (SELECT s.state_id FROM states s WHERE s.code = src.state_code);



ALTER TABLE congressional_districts DISABLE KEYS;

INSERT INTO congressional_districts (congressional_district_id, code, state_id)
     SELECT dp_get_next_sequence_id("locations"), district_code, s.state_id
       FROM zip4s.congressional_district_source cds, states s
      WHERE s.code = cds.state_code
        AND cds.district_code NOT LIKE 'S_';

ALTER TABLE congressional_districts ENABLE KEYS;



ALTER TABLE zips DISABLE KEYS;

INSERT INTO zips (zip_id, zip, state_id)
     SELECT dp_get_next_sequence_id("locations"), z.zip, z.state_id
       FROM (SELECT DISTINCT zip, state_id
               FROM zip4s.zip4_source) z;

ALTER TABLE zips ENABLE KEYS;



ALTER TABLE cities DISABLE KEYS;

INSERT INTO cities (city_id, name, fips, state_id, parent_city_id)
     SELECT dp_get_next_sequence_id("locations"), z.city_name, z.city_fips, z.state_id, NULL
       FROM (SELECT DISTINCT city_name, city_fips, state_id
               FROM zip4s.zip4_source) z;

ALTER TABLE cities ENABLE KEYS;



ALTER TABLE counties DISABLE KEYS;

INSERT INTO counties (county_id, name, fips, state_id)
     SELECT dp_get_next_sequence_id("locations"), z.county_name, z.county_fips, z.state_id
       FROM (SELECT DISTINCT county_name, county_fips, state_id
               FROM zip4s.zip4_source) z;

ALTER TABLE counties ENABLE KEYS;



ALTER TABLE zip4s DISABLE KEYS;

INSERT INTO zip4s (zip_id, zip4, city_id, county_id, congressional_district_id)
     SELECT z.zip_id, src.zip4, ct.city_id, cnt.county_id, cd.congressional_district_id
       FROM zip4s.zip4_source src, zips z, cities ct, counties cnt, congressional_districts cd
      WHERE z.zip = src.zip
        AND ct.state_id = src.state_id
        AND ct.name = src.city_name
        AND cnt.state_id = src.state_id
        AND cnt.fips = src.county_fips
        AND cd.state_id = src.state_id
        AND cd.code = src.congressional_district;

ALTER TABLE zip4s ENABLE KEYS;
