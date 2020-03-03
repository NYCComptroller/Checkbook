USE zip4s;


-- *****************************************************************************
--   Deleting last 'empty' line imported form .scv files
-- *****************************************************************************
SELECT 'Deleting empty records ...' AS ' ';

DELETE 
  FROM zip4_source
 WHERE zip4 = '    ';

DELETE 
  FROM congressional_district_source
 WHERE state_fips = '  ';

SELECT 'Completed deleting empty records.' AS ' ';
