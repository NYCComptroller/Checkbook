USE zip4s;


SELECT 'Resolving data issues ...' AS ' ';

-- *****************************************************************************
--   County FIPS
-- *****************************************************************************
UPDATE zip4_source
   SET county_fips = '009'
 WHERE state_code = 'AR'
   AND county_fips = '213'
   AND county_name = 'Boone';

UPDATE zip4_source
   SET county_fips = '121'
 WHERE state_code = 'AR'
   AND county_fips = '181'
   AND county_name = 'Randolph';


UPDATE zip4_source
   SET county_fips = '001'
 WHERE state_code = 'DE'
   AND county_fips = '011'
   AND county_name = 'Kent';

UPDATE zip4_source
   SET county_fips = '005'
 WHERE state_code = 'DE'
   AND county_fips = '019'
   AND county_name = 'Sussex';


UPDATE zip4_source
   SET county_fips = '001'
 WHERE state_code = 'DC'
   AND county_fips = '027'
   AND county_name = 'District of Columbia';


UPDATE zip4_source
   SET county_fips = '051'
 WHERE state_code = 'IA'
   AND county_fips = '199'
   AND county_name = 'Davis';

UPDATE zip4_source
   SET county_fips = '177'
 WHERE state_code = 'IA'
   AND county_fips = '199'
   AND county_name = 'Van Buren';


UPDATE zip4_source
   SET county_fips = '111'
 WHERE state_code = 'LA'
   AND county_fips = '139'
   AND county_name = 'Union';


UPDATE zip4_source
   SET county_fips = '033'
 WHERE state_code = 'ND'
   AND county_fips = '109'
   AND county_name = 'Golden Valley';

UPDATE zip4_source
   SET county_fips = '077'
 WHERE state_code = 'ND'
   AND county_fips = '155'
   AND county_name = 'Richland';

UPDATE zip4_source
   SET county_fips = '077'
 WHERE state_code = 'ND'
   AND county_fips = '167'
   AND county_name = 'Richland';


UPDATE zip4_source
   SET county_fips = '099'
 WHERE state_code = 'SD'
   AND county_fips = '133'
   AND county_name = 'Minnehaha';


UPDATE zip4_source
   SET county_fips = '005'
 WHERE state_code = 'WY'
   AND county_fips = '075'
   AND county_name = 'Campbell';

SELECT 'Completed resolving data issues.' AS ' ';
