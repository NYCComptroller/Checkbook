-- For General Fund Revenue Trend

DROP TABLE IF EXISTS trends_gen_fund_revenue_temp;

CREATE TABLE trends_gen_fund_revenue_temp
(
  category character varying,
  fy_2014 numeric(20,2),
  fy_2013 numeric(20,2),
  fy_2012 numeric(20,2),
  fy_2011 numeric(20,2),
  fy_2010 numeric(20,2),
  fy_2009 numeric(20,2),
  fy_2008 numeric(20,2),
  fy_2007 numeric(20,2),
  fy_2006 numeric(20,2),
  fy_2005 numeric(20,2),
  fy_2004 numeric(20,2),
  fy_2003 numeric(20,2),
  fy_2002 numeric(20,2),
  fy_2001 numeric(20,2),
  fy_2000 numeric(20,2),
  fy_1999 numeric(20,2),
  fy_1998 numeric(20,2),
  fy_1997 numeric(20,2),
  fy_1996 numeric(20,2),
  fy_1995 numeric(20,2),
  fy_1994 numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint
)
DISTRIBUTED BY (category);

DROP TABLE IF EXISTS trends_gen_fund_revenue;

CREATE TABLE trends_gen_fund_revenue
(
  category character varying,
  fiscal_year smallint,
  amount numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint,
  display_yn char(1)
)
DISTRIBUTED BY (category);

/*
1)	Modified the attached source excel by adding display_order,highlight_yn,amount_display_type, indentation_level columns and populating the data in those columns. And also modified the header names.
2)	Removed commas by formatting the amount fields.
3)	Created the CSV of modified excel. Removed some special characters (e.g.   � in line 20 Personal Income. (Non-Resident City Employees))
4)	And then ran the below commands to populate the data in  trends_gen_fund_revenue_temp table and public. trends_gen_fund_revenue tables.
*/

COPY  trends_gen_fund_revenue_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_general_fund_revenues.csv' CSV HEADER QUOTE as '"';

-- 5)	Below are the commands to populate the data from trends_gen_fund_revenue_temp to trends_gen_fund_revenue table.
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;
INSERT INTO trends_gen_fund_revenue (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1994, fy_1994, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_revenue_temp;

--update trends_gen_fund_revenue set display_yn ='N' where fiscal_year <1997;
--update trends_gen_fund_revenue set display_yn ='Y' where fiscal_year >=1997;

update trends_gen_fund_revenue set display_yn ='Y' ;

DROP TABLE trends_gen_fund_revenue_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- For General Fund Expenditures

DROP TABLE IF EXISTS trends_gen_fund_expenditure_temp;

CREATE TABLE trends_gen_fund_expenditure_temp
(
  category character varying,
  fy_2014 numeric(20,2),
  fy_2013 numeric(20,2),
  fy_2012 numeric(20,2),
  fy_2011 numeric(20,2),
  fy_2010 numeric(20,2),
  fy_2009 numeric(20,2),
  fy_2008 numeric(20,2),
  fy_2007 numeric(20,2),
  fy_2006 numeric(20,2),
  fy_2005 numeric(20,2),
  fy_2004 numeric(20,2),
  fy_2003 numeric(20,2),
  fy_2002 numeric(20,2),
  fy_2001 numeric(20,2),
  fy_2000 numeric(20,2),
  fy_1999 numeric(20,2),
  fy_1998 numeric(20,2),
  fy_1997 numeric(20,2),
  fy_1996 numeric(20,2),
  fy_1995 numeric(20,2),
  fy_1994 numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint
)
DISTRIBUTED BY (category);

DROP TABLE IF EXISTS trends_gen_fund_expenditure;

CREATE TABLE trends_gen_fund_expenditure
(
  category character varying,
  fiscal_year smallint,
  amount numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint,
  display_yn char(1)
)
DISTRIBUTED BY (category);

/*
1)            Modified the attached source excel by adding display_order,highlight_yn,amount_display_type, indentation_level columns and populating the data in those columns. And also modified the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel. Removed some special characters (e.g.   � in line 20 Personal Income� (Non-Resident City Employees))
4)            And then ran the below commands to populate the data in  trends_gen_fund_expenditure_temp table and public. trends_gen_fund_expenditure tables. */

COPY  trends_gen_fund_expenditure_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_general_fund_expenditures.csv' CSV HEADER QUOTE as '"';

 -- 5)            Below are the commands to populate the data from trends_gen_fund_expenditure_temp to trends_gen_fund_expenditure table.
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;
INSERT INTO trends_gen_fund_expenditure (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1994, fy_1994, display_order, highlight_yn, amount_display_type, indentation_level from trends_gen_fund_expenditure_temp;

--update trends_gen_fund_expenditure set display_yn ='N' where fiscal_year <1997;
--update trends_gen_fund_expenditure set display_yn ='Y' where fiscal_year >=1997;

update trends_gen_fund_expenditure set display_yn ='Y';

DROP TABLE trends_gen_fund_expenditure_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- For Capital Projects

DROP TABLE IF EXISTS trends_capital_projects_temp;
CREATE TABLE trends_capital_projects_temp
(
  category character varying,
  fy_2014 numeric(20,2),
  fy_2013 numeric(20,2),
  fy_2012 numeric(20,2),
  fy_2011 numeric(20,2),
  fy_2010 numeric(20,2),
  fy_2009 numeric(20,2),
  fy_2008 numeric(20,2),
  fy_2007 numeric(20,2),
  fy_2006 numeric(20,2),
  fy_2005 numeric(20,2),
  fy_2004 numeric(20,2),
  fy_2003 numeric(20,2),
  fy_2002 numeric(20,2),
  fy_2001 numeric(20,2),
  fy_2000 numeric(20,2),
  fy_1999 numeric(20,2),
  fy_1998 numeric(20,2),
  fy_1997 numeric(20,2),
  fy_1996 numeric(20,2),
  fy_1995 numeric(20,2),
  fy_1994 numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint
)
DISTRIBUTED BY (category);

DROP TABLE IF EXISTS trends_capital_projects;

CREATE TABLE trends_capital_projects
(
  category character varying,
  fiscal_year smallint,
  amount numeric(20,2),
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint,
  display_yn char(1)
)
DISTRIBUTED BY (category);

/*
1)            Modified the attached source excel by adding display_order,highlight_yn,amount_display_type, indentation_level columns and populating the data in those columns. And also modified the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel. Removed some special characters (e.g.  in line 20 Personal Income (Non-Resident City Employees))
4)            And then ran the below commands to populate the data in  trends_capital_projects_temp table and public. trends_capital_projects tables.  */

COPY  trends_capital_projects_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_capital_projects.csv' CSV HEADER QUOTE as '"';

 -- 5)            Below are the commands to populate the data from trends_capital_projects_temp to trends_capital_projects table.
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;
INSERT INTO trends_capital_projects (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1994, fy_1994, display_order, highlight_yn, amount_display_type, indentation_level from trends_capital_projects_temp;

--update trends_capital_projects set display_yn ='N' where fiscal_year <1997;
--update trends_capital_projects set display_yn ='Y' where fiscal_year >=1997;

update trends_capital_projects set display_yn ='Y';

DROP TABLE trends_capital_projects_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- For Ratios of Outstanding debt

DROP TABLE IF EXISTS trends_ratios_outstanding_debt_temp;

CREATE TABLE trends_ratios_outstanding_debt_temp
(
  fiscal_year smallint,
  general_obligation_bonds numeric(20,2),
  revenue_bonds numeric(20,2),
  ECF numeric(20,2),
  MAC_debt numeric(20,2),
  TFA numeric(20,2),
  TSASC_debt numeric(20,2),
  STAR numeric(20,2),
  FSC numeric(20,2),
  SFC_debt numeric(20,2),
  HYIC_bonds_notes numeric(20,2),
  capital_leases_obligations numeric(20,2),
  IDA_bonds numeric(20,2),
  treasury_obligations numeric(20,2),
  total_primary_government numeric(20,2)
)
DISTRIBUTED BY (fiscal_year);

DROP TABLE IF EXISTS trends_ratios_outstanding_debt;

CREATE TABLE trends_ratios_outstanding_debt
(
  fiscal_year smallint,
  general_obligation_bonds numeric(20,2),
  revenue_bonds numeric(20,2),
  ECF numeric(20,2),
  MAC_debt numeric(20,2),
  TFA numeric(20,2),
  TSASC_debt numeric(20,2),
  STAR numeric(20,2),
  FSC numeric(20,2),
  SFC_debt numeric(20,2),
  HYIC_bonds_notes numeric(20,2),
  capital_leases_obligations numeric(20,2),
  IDA_bonds numeric(20,2),
  treasury_obligations numeric(20,2),
  total_primary_government numeric(20,2),
  display_yn char(1)
)
DISTRIBUTED BY (fiscal_year);

/*
1)            Modified the attached source excel y adding  the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in trends_ratios_outstanding_debt_temp table

*/
COPY  trends_ratios_outstanding_debt_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_ratios_outstanding_debt.csv' CSV HEADER QUOTE as '"';

-- 5)            Below are the commands to populate the data from trends_ratios_outstanding_debt_temp to trends_ratios_outstanding_debt table.

INSERT INTO trends_ratios_outstanding_debt  select * from trends_ratios_outstanding_debt_temp;


--update trends_ratios_outstanding_debt set display_yn ='N' where fiscal_year <1997;
--update trends_ratios_outstanding_debt set display_yn ='Y' where fiscal_year >=1997;

update trends_ratios_outstanding_debt set display_yn ='Y';

DROP TABLE trends_ratios_outstanding_debt_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- For Property tax Levies

DROP TABLE IF EXISTS trends_property_tax_levies_temp;

CREATE TABLE trends_property_tax_levies_temp
(
  fiscal_year smallint,
  tax_levied numeric(20,2),
  amount numeric(20,2),
  percentage_levy numeric(4,2),
  collected_subsequent_years numeric(20,2),
  levy_non_cash_adjustments numeric(20,2),
  collected_amount numeric(20,2),
  collected_percentage_levy numeric(4,2),
  uncollected_amount numeric(20,2)
)
DISTRIBUTED BY (fiscal_year);

DROP TABLE IF EXISTS trends_property_tax_levies;

CREATE TABLE trends_property_tax_levies
(
  fiscal_year smallint,
  tax_levied numeric(20,2),
  amount numeric(20,2),
  percentage_levy numeric(4,2),
  collected_subsequent_years numeric(20,2),
  levy_non_cash_adjustments numeric(20,2),
  collected_amount numeric(20,2),
  collected_percentage_levy numeric(4,2),
  uncollected_amount numeric(20,2),
  display_yn char(1)
)
DISTRIBUTED BY (fiscal_year);

/*
1)            Modified the attached source excel y adding  the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in trends_property_tax_levies_temp table
*/

COPY  trends_property_tax_levies_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_property_tax_levies.csv' CSV HEADER QUOTE as '"';

-- 5)            Below are the commands to populate the data from trends_property_tax_levies_temp to trends_property_tax_levies table.

INSERT INTO trends_property_tax_levies  select * from trends_property_tax_levies_temp;

--update trends_property_tax_levies set display_yn ='N' where fiscal_year <1997;
--update trends_property_tax_levies set display_yn ='Y' where fiscal_year >=1997;

update trends_property_tax_levies set display_yn ='Y';

DROP TABLE trends_property_tax_levies_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- For Personal Income  (Not Done as it was not matching with the previous file) (For now no change)

DROP TABLE IF EXISTS trends_personal_income_temp;

CREATE TABLE trends_personal_income_temp
(
  fips character varying,
  area character varying,
  fy_1969 int,
  fy_1970 int,
  fy_1971 int,
  fy_1972 int,
  fy_1973 int,
  fy_1974 int,
  fy_1975 int,
  fy_1976 int,
  fy_1977 int,
  fy_1978 int,
  fy_1979 int,
  fy_1980 int,
  fy_1981 int,
  fy_1982 int,
  fy_1983 int,
  fy_1984 int,
  fy_1985 int,
  fy_1986 int,
  fy_1987 int,
  fy_1988 int,
  fy_1989 int,
  fy_1990 int,
  fy_1991 int,
  fy_1992 int,
  fy_1993 int,
  fy_1994 int,
  fy_1995 int,
  fy_1996 int,
  fy_1997 int,
  fy_1998 int,
  fy_1999 int,
  fy_2000 int,
  fy_2001 int,
  fy_2002 int,
  fy_2003 int,
  fy_2004 int,
  fy_2005 int,
  fy_2006 int,
  fy_2007 int,
  fy_2008 int,
  fy_2009 int,
  fy_2010 int,
  fy_2011 int,
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint
)
DISTRIBUTED BY (fips);

DROP TABLE IF EXISTS trends_personal_income;

CREATE TABLE trends_personal_income
(
  fips character varying,
  area character varying,
  fiscal_year smallint,
  income_or_population int,
  display_order smallint,
  highlight_yn character(1),
  amount_display_type character(1),
  indentation_level smallint,
  display_yn char(1)
)
DISTRIBUTED BY (fips);

/*

1)            Modified the attached source excel by adding display_order,highlight_yn,amount_display_type, indentation_level columns and populating the data in those columns. And also modified the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in  trends_personal_income_temp table and public. trends_personal_income tables.

*/

COPY  trends_personal_income_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_personal_incomes.csv' CSV HEADER QUOTE as '"';

-- 5)            Below are the commands to populate the data from trends_personal_income_temp to trends_personal_income table.

INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2012, 0, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1994, fy_1994, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1993, fy_1993, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1992, fy_1992, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1991, fy_1991, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1990, fy_1990, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1989, fy_1989, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1988, fy_1988, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1987, fy_1987, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1986, fy_1986, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1985, fy_1985, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1984, fy_1984, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1983, fy_1983, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1982, fy_1982, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1981, fy_1981, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1980, fy_1980, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1979, fy_1979, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1978, fy_1978, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1977, fy_1977, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1976, fy_1976, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1975, fy_1975, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1974, fy_1974, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1973, fy_1973, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1972, fy_1972, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1971, fy_1971, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1970, fy_1970, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;
INSERT INTO trends_personal_income ( fips, area,  fiscal_year, income_or_population, display_order, highlight_yn, amount_display_type, indentation_level) select  fips, area, 1969, fy_1969, display_order, highlight_yn, amount_display_type, indentation_level from trends_personal_income_temp;

--update trends_personal_income set display_yn ='N' where fiscal_year < 1980;
--update trends_personal_income set display_yn ='Y' where fiscal_year>=1980;

update trends_personal_income set display_yn ='Y' ;

DROP TABLE trends_personal_income_temp;
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 6) Trends_collection_cancellation_abatements

DROP TABLE IF EXISTS trends_collection_cancellation_abatements_temp;

CREATE TABLE trends_collection_cancellation_abatements_temp
(fiscal_year smallint,
tax_levy numeric(20,2),
collection numeric(4,2),
cancellations numeric(4,2),
abatement_and_discounts_1 numeric(4,2),
uncollected_balance_percent numeric(4,2)
)
Distributed by (fiscal_year);

DROP TABLE IF EXISTS trends_collection_cancellation_abatements;

CREATE TABLE trends_collection_cancellation_abatements
(fiscal_year smallint,
tax_levy numeric(20,2),
collection numeric(4,2),
cancellations numeric(4,2),
abatement_and_discounts_1 numeric(4,2),
uncollected_balance_percent numeric(4,2),
display_yn char(1)
)
Distributed by (fiscal_year);


/*
1)            Modified the attached source excel by adding  the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in trends_collection_cancellation_abatements_temp table
*/


COPY  trends_collection_cancellation_abatements_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_collection_cancellation_abatements.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_collection_cancellation_abatements  select * from trends_collection_cancellation_abatements_temp;

--update  trends_collection_cancellation_abatements set display_yn ='N' where fiscal_year <1997;
--update  trends_collection_cancellation_abatements set display_yn ='Y' where fiscal_year >=1997;

update  trends_collection_cancellation_abatements set display_yn ='Y';

DROP TABLE trends_collection_cancellation_abatements_temp;
----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 7) trends_employment_status_of_resident_population


DROP TABLE IF EXISTS trends_employment_status_of_resident_population_temp;

CREATE TABLE trends_employment_status_of_resident_population_temp
(
fiscal_year smallint,
civilian_labor_force_new_york_city_employed numeric(20,2),
civilian_labor_force_unemployed numeric(20,2),
unemployment_rate_city_percent	numeric(4,2),
unemployment_rate_united_states_percent numeric(4,2)
)
Distributed by (fiscal_year);

DROP TABLE IF EXISTS trends_employment_status_of_resident_population;

CREATE TABLE trends_employment_status_of_resident_population
(
fiscal_year smallint,
civilian_labor_force_new_york_city_employed numeric(20,2),
civilian_labor_force_unemployed numeric(20,2),
unemployment_rate_city_percent	numeric(4,2),
unemployment_rate_united_states_percent numeric(4,2),
display_yn char(1)
)
Distributed by (fiscal_year);

/*
1)            Modified the attached source excel by adding  the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in trends_employment_status_of_resident_population_temp table
*/


COPY  trends_employment_status_of_resident_population_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_employment_status_of_resident_population.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_employment_status_of_resident_population  select * from trends_employment_status_of_resident_population_temp;

--update  trends_employment_status_of_resident_population set display_yn ='N' where fiscal_year <1996;
--update  trends_employment_status_of_resident_population set display_yn ='Y' where fiscal_year >=1996;

update  trends_employment_status_of_resident_population set display_yn ='Y';

DROP TABLE trends_employment_status_of_resident_population_temp;

------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 8) trends_non_agricultural_wage_salary_employement

DROP TABLE IF EXISTS trends_non_agricultural_wage_salary_employement_temp;
CREATE TABLE trends_non_agricultural_wage_salary_employement_temp
(
category character varying,
fy_2014 numeric(20,2),
fy_2013 numeric(20,2),
fy_2012 numeric(20,2),
fy_2011 numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);


DROP TABLE IF EXISTS trends_non_agricultural_wage_salary_employement;
CREATE TABLE trends_non_agricultural_wage_salary_employement
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);


COPY  trends_non_agricultural_wage_salary_employement_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_non_agricultural_wage_salary_employement.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;
INSERT INTO trends_non_agricultural_wage_salary_employement (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_non_agricultural_wage_salary_employement_temp;

--update  trends_non_agricultural_wage_salary_employement set display_yn ='N' where fiscal_year <1997;
--update  trends_non_agricultural_wage_salary_employement set display_yn ='Y' where fiscal_year >=1997;

update  trends_non_agricultural_wage_salary_employement set display_yn ='N' where fiscal_year <1997;
update  trends_non_agricultural_wage_salary_employement set display_yn ='Y' where fiscal_year >=1997;

DROP TABLE trends_non_agricultural_wage_salary_employement_temp;
-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 9)

DROP TABLE IF EXISTS trends_numberofcityemployees_temp;
CREATE TABLE trends_numberofcityemployees_temp
(
category character varying,
fy_2014 numeric(20,2),
fy_2013 numeric(20,2),
fy_2012 numeric(20,2),
fy_2011 numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
fy_1994 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);


DROP TABLE IF EXISTS trends_numberofcityemployees;
CREATE TABLE trends_numberofcityemployees
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);


COPY  trends_numberofcityemployees_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_numberofcityemployees.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;
INSERT INTO trends_numberofcityemployees (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1994, fy_1994, display_order, highlight_yn, amount_display_type, indentation_level from trends_numberofcityemployees_temp;


--update  trends_numberofcityemployees  set display_yn ='N' where fiscal_year <1997;
--update  trends_numberofcityemployees  set display_yn ='Y' where fiscal_year >=1997;

update  trends_numberofcityemployees  set display_yn ='Y';

DROP TABLE trends_numberofcityemployees_temp;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 10)  trends_new_york_city_educational_construction

DROP TABLE IF EXISTS trends_new_york_city_educational_construction_temp;
CREATE TABLE trends_new_york_city_educational_construction_temp
(
fiscal_year smallint,
rental_revenue	numeric(20,2),
interest_revenue	numeric(20,2),
total_revenue numeric(20,2),
interest numeric(20,2),
pricipal numeric(20,2),
total	numeric(20,2),
operating_expenses numeric(20,2),
total_to_be_covered numeric(20,2),
coverage_ratio numeric(20,2)
)
Distributed by (fiscal_year);


DROP TABLE IF EXISTS trends_new_york_city_educational_construction;
CREATE TABLE trends_new_york_city_educational_construction
(
fiscal_year smallint,
rental_revenue	numeric(20,2),
interest_revenue	numeric(20,2),
total_revenue numeric(20,2),
interest numeric(20,2),
pricipal numeric(20,2),
total	numeric(20,2),
operating_expenses numeric(20,2),
total_to_be_covered numeric(20,2),
coverage_ratio numeric(20,2),
display_yn char(1)
)
Distributed by (fiscal_year);

COPY  trends_new_york_city_educational_construction_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_new_york_city_educational_construction.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_new_york_city_educational_construction  select * from trends_new_york_city_educational_construction_temp;

--update  trends_new_york_city_educational_construction set display_yn ='N' where fiscal_year <2005;
--update  trends_new_york_city_educational_construction set display_yn ='Y' where fiscal_year >=2005;

update  trends_new_york_city_educational_construction set display_yn ='Y' ;

DROP TABLE trends_new_york_city_educational_construction_temp;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 11) trends_changes_in_fund_balances

DROP TABLE IF EXISTS trends_changes_in_fund_balances_temp;
CREATE TABLE trends_changes_in_fund_balances_temp
(
category character varying,
fy_2014 numeric(20,2),
fy_2013 numeric(20,2),
fy_2012 numeric(20,2),
fy_2011 numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
currency_symbol character(1)
)
Distributed by (category);


DROP TABLE IF EXISTS trends_changes_in_fund_balances;
CREATE TABLE trends_changes_in_fund_balances
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
currency_symbol character(1),
display_yn char(1)
)
DISTRIBUTED BY (category);


COPY  trends_changes_in_fund_balances_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_changes_in_fund_balances.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;
INSERT INTO trends_changes_in_fund_balances (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level,currency_symbol from trends_changes_in_fund_balances_temp;


--update  trends_changes_in_fund_balances set display_yn ='N' where fiscal_year <1997;
--update  trends_changes_in_fund_balances set display_yn ='Y' where fiscal_year >=1997;

update  trends_changes_in_fund_balances set display_yn ='Y' ;

DROP TABLE trends_changes_in_fund_balances_temp;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 12) trends_capital_assets_statistics_function_program

DROP TABLE IF EXISTS trends_capital_assets_statistics_function_program_temp;
CREATE TABLE trends_capital_assets_statistics_function_program_temp
(
category character varying,
fy_2014 numeric(20,2),
fy_2013 numeric(20,2),
fy_2012 numeric(20,2),
fy_2011 numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
superscript_yn character(1)
)
Distributed by (category);


DROP TABLE IF EXISTS trends_capital_assets_statistics_function_program;
CREATE TABLE trends_capital_assets_statistics_function_program
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
superscript_yn character(1),
display_yn char(1)
)
DISTRIBUTED BY (category);

	COPY  trends_capital_assets_statistics_function_program_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_capital_assets_statistics_function_program.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;
INSERT INTO trends_capital_assets_statistics_function_program (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level,superscript_yn from trends_capital_assets_statistics_function_program_temp;

--update  trends_capital_assets_statistics_function_program set display_yn ='N' where fiscal_year < 2002;
--update  trends_capital_assets_statistics_function_program set display_yn ='Y' where fiscal_year >= 2002;

update  trends_capital_assets_statistics_function_program set display_yn ='Y';

DROP TABLE trends_capital_assets_statistics_function_program_temp;
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 13) trends_assesed_valuation_tax_rate_class


DROP TABLE IF EXISTS trends_assesed_valuation_tax_rate_class_temp;
CREATE TABLE trends_assesed_valuation_tax_rate_class_temp
(
category character varying,
fy2014assesed_value_million numeric(20,2),
fy2014percentage_taxable_real_estate numeric(20,2),
fy2014_direct_tax_rate numeric(20,2),
fy2013assesed_value_million numeric(20,2),
fy2013percentage_taxable_real_estate numeric(20,2),
fy2013_direct_tax_rate numeric(20,2),
fy2012assesed_value_million numeric(20,2),
fy2012percentage_taxable_real_estate numeric(20,2),
fy2012_direct_tax_rate numeric(20,2),
fy2011assesed_value_million numeric(20,2),
fy2011percentage_taxable_real_estate numeric(20,2),
fy2011_direct_tax_rate numeric(20,2),
fy2010assesed_value_million numeric(20,2),
fy2010percentage_taxable_real_estate	 numeric(20,2),
fy2010_direct_tax_rate numeric(20,2),
fy2009assesed_value_million numeric(20,2),
fy2009percentage_taxable_real_estate numeric(20,2),
fy2009_direct_tax_rate	 numeric(20,2),
fy2008assesed_value_million numeric(20,2),
fy2008percentage_taxable_real_estate numeric(20,2),
fy2008_direct_tax_rate numeric(20,2),
fy2007assesed_value_million numeric(20,2),
fy2007percentage_taxable_real_estate	 numeric(20,2),
fy2007_direct_tax_rate numeric(20,2),
fy2006assesed_value_million numeric(20,2),
fy2006percentage_taxable_real_estate numeric(20,2),
fy2006_direct_tax_rate	 numeric(20,2),
fy2005assesed_value_million numeric(20,2),
fy2005percentage_taxable_real_estate numeric(20,2),
fy2005_direct_tax_rate numeric(20,2),
fy2004assesed_value_million numeric(20,2),
fy2004percentage_taxable_real_estate numeric(20,2),
fy2004_direct_tax_rate numeric(20,2),
fy2003assesed_value_million numeric(20,2),
fy2003percentage_taxable_real_estate	 numeric(20,2),
fy2003_direct_tax_rate numeric(20,2),
fy2002assesed_value_million numeric(20,2),
fy2002percentage_taxable_real_estate numeric(20,2),
fy2002_direct_tax_rate numeric(20,2),
fy2001assesed_value_million numeric(20,2),
fy2001percentage_taxable_real_estate	 numeric(20,2),
fy2001_direct_tax_rate	 numeric(20,2),
fy2000assesed_value_million	 numeric(20,2),
fy2000percentage_taxable_real_estate numeric(20,2),
fy2000_direct_tax_rate	 numeric(20,2),
fy1999assesed_value_million numeric(20,2),
fy1999percentage_taxable_real_estate numeric(20,2),
fy1999_direct_tax_rate numeric(20,2),
fy1998assesed_value_million numeric(20,2),
fy1998percentage_taxable_real_estate numeric(20,2),
fy1998_direct_tax_rate numeric(20,2),
fy1997assesed_value_million numeric(20,2),
fy1997percentage_taxable_real_estate	 numeric(20,2),
fy1997_direct_tax_rate numeric(20,2),
fy1996assesed_value_million numeric(20,2),
fy1996percentage_taxable_real_estate numeric(20,2),
fy1996_direct_tax_rate numeric(20,2),
fy1995assesed_value_million numeric(20,2),
fy1995percentage_taxable_real_estate numeric(20,2),
fy1995_direct_tax_rate numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
distributed by (category);


DROP TABLE IF EXISTS trends_assesed_valuation_tax_rate_class;


CREATE TABLE trends_assesed_valuation_tax_rate_class
(
category character varying,
fiscal_year smallint,
assesed_value_million_amount numeric(20,2),
percentage_taxable_real_estate numeric(20,2),
direct_tax_rate numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);

COPY  trends_assesed_valuation_tax_rate_class_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_assesed_valuation_tax_rate_class.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014,fy2014assesed_value_million,fy2014percentage_taxable_real_estate,fy2014_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013,fy2013assesed_value_million,fy2013percentage_taxable_real_estate,fy2013_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012,fy2012assesed_value_million,fy2012percentage_taxable_real_estate,fy2012_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011,fy2011assesed_value_million,fy2011percentage_taxable_real_estate,fy2011_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010,fy2010assesed_value_million,fy2010percentage_taxable_real_estate,fy2010_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009,fy2009assesed_value_million,fy2009percentage_taxable_real_estate,fy2009_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008,fy2008assesed_value_million,fy2008percentage_taxable_real_estate,fy2008_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007,fy2007assesed_value_million,fy2007percentage_taxable_real_estate,fy2007_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006,fy2006assesed_value_million,fy2006percentage_taxable_real_estate,fy2006_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005,fy2005assesed_value_million,fy2005percentage_taxable_real_estate,fy2005_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004,fy2004assesed_value_million,fy2004percentage_taxable_real_estate,fy2004_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003,fy2003assesed_value_million,fy2003percentage_taxable_real_estate,fy2003_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002,fy2002assesed_value_million,fy2002percentage_taxable_real_estate,fy2002_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001,fy2001assesed_value_million,fy2001percentage_taxable_real_estate,fy2001_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000,fy2000assesed_value_million,fy2000percentage_taxable_real_estate,fy2000_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999,fy1999assesed_value_million,fy1999percentage_taxable_real_estate,fy1999_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998,fy1998assesed_value_million,fy1998percentage_taxable_real_estate,fy1998_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997,fy1997assesed_value_million,fy1997percentage_taxable_real_estate,fy1997_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996,fy1996assesed_value_million,fy1996percentage_taxable_real_estate,fy1996_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;
INSERT INTO trends_assesed_valuation_tax_rate_class (category, fiscal_year, assesed_value_million_amount,percentage_taxable_real_estate,direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995,fy1995assesed_value_million,fy1995percentage_taxable_real_estate,fy1995_direct_tax_rate, display_order, highlight_yn, amount_display_type, indentation_level from trends_assesed_valuation_tax_rate_class_temp;

--update  trends_assesed_valuation_tax_rate_class set display_yn ='N' where fiscal_year <1997;
--update  trends_assesed_valuation_tax_rate_class set display_yn ='Y' where fiscal_year >=1997;

update  trends_assesed_valuation_tax_rate_class set display_yn ='Y';

alter table trends_assesed_valuation_tax_rate_class add column superscript_value character(1);

update trends_assesed_valuation_tax_rate_class set superscript_value ='1' where category ='Total' and fiscal_year not in(2013,2012,2011,1995);
update trends_assesed_valuation_tax_rate_class set superscript_value ='1' where display_order = 40 and fiscal_year =1995;


DROP TABLE trends_assesed_valuation_tax_rate_class_temp;

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 14) trends_assesed_estimated_actual_taxable_property


DROP TABLE IF EXISTS trends_assesed_estimated_actual_taxable_property_temp;
CREATE TABLE trends_assesed_estimated_actual_taxable_property_temp
(
fiscal_year smallint,
class_one numeric(20,1),
class_two numeric(20,1),
class_three numeric(20,1),
class_four numeric(20,1),
less_tax_exempt_property numeric(20,1),
total_taxable_assesed_value numeric(20,1),
total_direct_tax_1 numeric(20,2),
estimated_actual_taxable_value numeric(20,1),
assesed_value_percentage numeric(20,2)
)
Distributed by (fiscal_year);


DROP TABLE IF EXISTS trends_assesed_estimated_actual_taxable_property;
CREATE TABLE trends_assesed_estimated_actual_taxable_property
(
fiscal_year smallint,
class_one numeric(20,1),
class_two numeric(20,1),
class_three numeric(20,1),
class_four numeric(20,1),
less_tax_exempt_property numeric(20,1),
total_taxable_assesed_value numeric(20,1),
total_direct_tax_1 numeric(20,2),
estimated_actual_taxable_value numeric(20,1),
assesed_value_percentage numeric(20,2),
display_yn char(1)
)
Distributed by (fiscal_year);

/*
1)            Modified the attached source excel by adding  the header names.
2)            Removed commas by formatting the amount fields.
3)            Created the CSV of modified excel.
4)            And then ran the below commands to populate the data in trends_assesed_estimated_actual_taxable_property table
*/


COPY  trends_assesed_estimated_actual_taxable_property_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_assesed_estimated_actual_taxable_property.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_assesed_estimated_actual_taxable_property  select * from trends_assesed_estimated_actual_taxable_property_temp;

--update  trends_assesed_estimated_actual_taxable_property set display_yn ='N' where fiscal_year <1997;
--update  trends_assesed_estimated_actual_taxable_property set display_yn ='Y' where fiscal_year >=1997;

update  trends_assesed_estimated_actual_taxable_property set display_yn ='Y';

DROP TABLE trends_assesed_estimated_actual_taxable_property_temp;
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 15)  trends_nyc_population_temp

DROP TABLE IF EXISTS trends_nyc_population_temp;
CREATE TABLE trends_nyc_population_temp
(
fiscal_year smallint,
united_states integer,
percentage_change_from_prior_period numeric(4,2),
city_of_new_york integer,
percentage_change_prior_period numeric(4,2)
)
Distributed by (fiscal_year);


DROP TABLE IF EXISTS trends_nyc_population;
CREATE TABLE trends_nyc_population
(
fiscal_year smallint,
united_states integer,
percentage_change_from_prior_period numeric(4,2),
city_of_new_york integer,
percentage_change_prior_period numeric(4,2),
display_yn char(1)
)
Distributed by (fiscal_year);



COPY  trends_nyc_population_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_nyc_population.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_nyc_population  select * from trends_nyc_population_temp;

update  trends_nyc_population set display_yn ='Y';

DROP TABLE trends_nyc_population_temp;

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 16)  trends_property_tax

DROP TABLE IF EXISTS trends_property_tax_temp;
CREATE TABLE trends_property_tax_temp
(
fiscal_year smallint,
basic_rate numeric(4,2),
obligation_debt numeric(4,2),
total_direct numeric(4,2)
)
Distributed by (fiscal_year);


DROP TABLE IF EXISTS trends_property_tax;
CREATE TABLE trends_property_tax
(
fiscal_year smallint,
basic_rate numeric(4,2),
obligation_debt numeric(4,2),
total_direct numeric(4,2),
display_yn char(1)
)
Distributed by (fiscal_year);


COPY  trends_property_tax_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_property_tax.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_property_tax select * from trends_property_tax_temp;

--update  trends_property_tax set display_yn ='N' where fiscal_year <1997;
--update  trends_property_tax set display_yn ='Y' where fiscal_year >=1997;

update  trends_property_tax set display_yn ='Y';

DROP TABLE trends_property_tax_temp;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 17) trends_person_receiving_pubic_assistance

DROP TABLE IF EXISTS trends_person_receiving_pubic_assistance_temp;
CREATE TABLE trends_person_receiving_pubic_assistance_temp
(
fiscal_year smallint,
public_assistance integer,
SSI integer
)
Distributed by (fiscal_year);

DROP TABLE IF EXISTS trends_person_receiving_pubic_assistance;
CREATE TABLE trends_person_receiving_pubic_assistance
(
fiscal_year smallint,
public_assistance integer,
SSI integer,
display_yn char(1)
)
Distributed by (fiscal_year);


COPY  trends_person_receiving_pubic_assistance_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_person_recieving_public_asssistance.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_person_receiving_pubic_assistance select * from trends_person_receiving_pubic_assistance_temp;

--update trends_person_receiving_pubic_assistance set display_yn ='Y' where fiscal_year < 2002;
--update trends_person_receiving_pubic_assistance set display_yn ='Y' where fiscal_year >= 2002;

update trends_person_receiving_pubic_assistance set display_yn ='Y';

DROP TABLE trends_person_receiving_pubic_assistance_temp;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 18)

DROP TABLE IF EXISTS trends_pledged_revenue_temp;
CREATE TABLE trends_pledged_revenue_temp
(
fiscal_year smallint,
PIT_revenue integer,
sales_tax_revenue integer,
total_receipt	 integer,
other integer,
investment_earnings integer,
total_revenue integer,
interest integer,
pricipal integer,
total integer,
operating_expenses integer,
total_to_be_covered integer
)
Distributed by (fiscal_year);


DROP TABLE IF EXISTS trends_pledged_revenue;
CREATE TABLE trends_pledged_revenue
(
fiscal_year smallint,
PIT_revenue integer,
sales_tax_revenue integer,
total_receipt	 integer,
other integer,
investment_earnings integer,
total_revenue integer,
interest integer,
pricipal integer,
total integer,
operating_expenses integer,
total_to_be_covered integer,
display_yn char(1)
)
Distributed by (fiscal_year);




COPY  trends_pledged_revenue_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_pledged_revenue.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_pledged_revenue select * from trends_pledged_revenue_temp;
--update  trends_pledged_revenue set display_yn ='N' where fiscal_year <2002;
--update  trends_pledged_revenue set display_yn ='Y' where fiscal_year >=2002;

update  trends_pledged_revenue set display_yn ='Y';

DROP TABLE trends_pledged_revenue_temp;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 19)

DROP TABLE IF EXISTS trends_uncollected_parking_violation_temp;
CREATE TABLE trends_uncollected_parking_violation_temp
(
category varchar,
fy_2014	numeric(20,2),
fy_2013	numeric(20,2),
fy_2012	numeric(20,2),
fy_2011	numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);

DROP TABLE IF EXISTS trends_uncollected_parking_violation;
CREATE TABLE trends_uncollected_parking_violation
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);

COPY  trends_uncollected_parking_violation_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_uncollected_parking_violation.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;
INSERT INTO trends_uncollected_parking_violation (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_uncollected_parking_violation_temp;

--update  trends_uncollected_parking_violation set display_yn ='N' where fiscal_year <1997;
--update  trends_uncollected_parking_violation set display_yn ='Y' where fiscal_year >=1997;

update  trends_uncollected_parking_violation set display_yn ='Y';

DROP TABLE trends_uncollected_parking_violation_temp;
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 20)  trends_changes_net_assets

DROP TABLE IF EXISTS trends_changes_net_assets_temp;
CREATE TABLE trends_changes_net_assets_temp
(
category varchar,
fy_2014	numeric(20,2),
fy_2013	numeric(20,2),
fy_2012	numeric(20,2),
fy_2011	numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);

DROP TABLE IF EXISTS trends_changes_net_assets;
CREATE TABLE trends_changes_net_assets
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);

COPY  trends_changes_net_assets_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_changes_net_assets.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;
INSERT INTO trends_changes_net_assets (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_changes_net_assets_temp;

--update  trends_changes_net_assets set display_yn ='N' where fiscal_year < 2002;
--update  trends_changes_net_assets set display_yn ='Y' where fiscal_year >= 2002;


update  trends_changes_net_assets set display_yn ='Y' ;

DROP TABLE trends_changes_net_assets_temp;

----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 21) trends_government_funds

DROP TABLE IF EXISTS trends_government_funds_temp;
CREATE TABLE trends_government_funds_temp
(
category varchar,
fy_2014 numeric(20,2),
fy_2013 numeric(20,2),
fy_2012 numeric(20,2),
fy_2011 numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);

DROP TABLE IF EXISTS trends_government_funds;
CREATE TABLE trends_government_funds
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);

COPY  trends_government_funds_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_government_funds.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;
INSERT INTO trends_government_funds (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_government_funds_temp;


--update  trends_government_funds set display_yn ='N' where fiscal_year < 1997;
--update  trends_government_funds set display_yn ='Y' where fiscal_year >= 1997;

update  trends_government_funds set display_yn ='Y' ;

DROP TABLE trends_government_funds_temp;

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------


-- 22)  trends_hudson_yards_infrastructure

DROP TABLE IF EXISTS trends_hudson_yards_infrastructure_temp;
CREATE TABLE trends_hudson_yards_infrastructure_temp
(
fiscal_year	smallint,
dib_revenue_1 numeric(20,2),
tep_revenue_2 numeric(20,2),
isp_revenue_3 numeric(20,2),
pilomrt_payment numeric(20,2),
other_4	 numeric(20,2),
investment_earnings numeric(20,2),
total_revenue numeric(20,2),
interest	 numeric(20,2),
principal	 numeric(20,2),
total numeric(20,2),
operating_expenses numeric(20,2),
total_to_be_covered numeric(20,2),
coverage_on_total_revenue_5 numeric(20,2)
)
Distributed by (fiscal_year);

DROP TABLE IF EXISTS trends_hudson_yards_infrastructure;
CREATE TABLE trends_hudson_yards_infrastructure
(
fiscal_year	smallint,
dib_revenue_1 numeric(20,2),
tep_revenue_2 numeric(20,2),
isp_revenue_3 numeric(20,2),
pilomrt_payment numeric(20,2),
other_4	 numeric(20,2),
investment_earnings numeric(20,2),
total_revenue numeric(20,2),
interest	 numeric(20,2),
principal	 numeric(20,2),
total numeric(20,2),
operating_expenses numeric(20,2),
total_to_be_covered numeric(20,2),
coverage_on_total_revenue_5 numeric(20,2),
display_yn char(1)
)
Distributed by (fiscal_year);

COPY  trends_hudson_yards_infrastructure_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_hudson_yards_infrastructure.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_hudson_yards_infrastructure  select * from trends_hudson_yards_infrastructure_temp;

update trends_hudson_yards_infrastructure set display_yn ='Y';


alter table trends_hudson_yards_infrastructure add column superscript_value character(1);
update trends_hudson_yards_infrastructure set superscript_value ='1' where fiscal_year  in(2009,2010,2011,2012);

DROP TABLE trends_hudson_yards_infrastructure_temp;

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
-- 23)

DROP TABLE IF EXISTS trends_legal_debt_margin_temp;
CREATE TABLE trends_legal_debt_margin_temp
(
category varchar,
fy_2014	numeric(20,2),
fy_2013	numeric(20,2),
fy_2012	numeric(20,2),
fy_2011	numeric(20,2),
fy_2010 numeric(20,2),
fy_2009 numeric(20,2),
fy_2008 numeric(20,2),
fy_2007 numeric(20,2),
fy_2006 numeric(20,2),
fy_2005 numeric(20,2),
fy_2004 numeric(20,2),
fy_2003 numeric(20,2),
fy_2002 numeric(20,2),
fy_2001 numeric(20,2),
fy_2000 numeric(20,2),
fy_1999 numeric(20,2),
fy_1998 numeric(20,2),
fy_1997 numeric(20,2),
fy_1996 numeric(20,2),
fy_1995 numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint
)
Distributed by (category);

DROP TABLE IF EXISTS trends_legal_debt_margin;
CREATE TABLE trends_legal_debt_margin
(
category character varying,
fiscal_year smallint,
amount numeric(20,2),
display_order smallint,
highlight_yn character(1),
amount_display_type character(1),
indentation_level smallint,
display_yn char(1)
)
DISTRIBUTED BY (category);


COPY  trends_legal_debt_margin_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_legal_debt_margin.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2014, fy_2014, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2013, fy_2013, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2012, fy_2012, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2011, fy_2011, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2010, fy_2010, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2009, fy_2009, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2008, fy_2008, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2007, fy_2007, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2006, fy_2006, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2005, fy_2005, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2004, fy_2004, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2003, fy_2003, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2002, fy_2002, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2001, fy_2001, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 2000, fy_2000, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1999, fy_1999, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1998, fy_1998, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1997, fy_1997, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1996, fy_1996, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;
INSERT INTO trends_legal_debt_margin (category, fiscal_year, amount, display_order, highlight_yn, amount_display_type, indentation_level) select trim(category), 1995, fy_1995, display_order, highlight_yn, amount_display_type, indentation_level from trends_legal_debt_margin_temp;


--update  trends_legal_debt_margin set display_yn ='N' where fiscal_year <1997;
--update  trends_legal_debt_margin set display_yn ='Y' where fiscal_year >=1997;

update  trends_legal_debt_margin set display_yn ='Y';

DROP TABLE trends_legal_debt_margin_temp;

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- 24) trends_ratios_general_bonded_debt_outstanding

DROP TABLE IF EXISTS trends_ratios_general_bonded_debt_outstanding_temp;
CREATE TABLE trends_ratios_general_bonded_debt_outstanding_temp
(
fiscal_year smallint,
general_bonded_debt numeric(20,2),
debt_by_revenue_ot_prop_tax numeric(20,2),
general_obligation_bonds numeric(20,2),
percentage_atcual_taxable_property numeric(20,2),
per_capita_general_obligations numeric(20,2)
)
Distributed by (fiscal_year);

DROP TABLE IF EXISTS trends_ratios_general_bonded_debt_outstanding;
CREATE TABLE trends_ratios_general_bonded_debt_outstanding
(
fiscal_year smallint,
general_bonded_debt numeric(20,2),
debt_by_revenue_ot_prop_tax numeric(20,2),
general_obligation_bonds numeric(20,2),
percentage_atcual_taxable_property numeric(20,2),
per_capita_general_obligations numeric(20,2),
display_yn char(1)
)
Distributed by (fiscal_year);


COPY  trends_ratios_general_bonded_debt_outstanding_temp FROM '/home/gpadmin/GREENPLUM/Checkbook/TRENDS_DATA/trends_ratios_general_bonded_debt_outstanding.csv' CSV HEADER QUOTE as '"';

INSERT INTO trends_ratios_general_bonded_debt_outstanding select * from trends_ratios_general_bonded_debt_outstanding_temp;


--update  trends_ratios_general_bonded_debt_outstanding set display_yn ='N' where fiscal_year <1997;
--update  trends_ratios_general_bonded_debt_outstanding set display_yn ='Y' where fiscal_year >=1997;

update  trends_ratios_general_bonded_debt_outstanding set display_yn ='Y';

DROP TABLE trends_ratios_general_bonded_debt_outstanding_temp;


/*  Verification Scripts

select table_name from information_schema.tables where table_name ilike 'trends_%'


SELECT 'trends_gen_fund_revenue' as table_name, count(*) FROM trends_gen_fund_revenue UNION
SELECT 'trends_gen_fund_expenditure' as table_name, count(*) FROM trends_gen_fund_expenditure UNION
SELECT 'trends_capital_projects' as table_name, count(*) FROM trends_capital_projects UNION
SELECT 'trends_ratios_outstanding_debt' as table_name, count(*) FROM trends_ratios_outstanding_debt UNION
SELECT 'trends_property_tax_levies' as table_name, count(*) FROM trends_property_tax_levies UNION
SELECT 'trends_personal_income' as table_name, count(*) FROM trends_personal_income UNION
SELECT 'trends_collection_cancellation_abatements' as table_name, count(*) FROM trends_collection_cancellation_abatements UNION
SELECT 'trends_employment_status_of_resident_population' as table_name, count(*) FROM trends_employment_status_of_resident_population UNION
SELECT 'trends_non_agricultural_wage_salary_employement' as table_name, count(*) FROM trends_non_agricultural_wage_salary_employement UNION
SELECT 'trends_numberofcityemployees' as table_name, count(*) FROM trends_numberofcityemployees UNION
SELECT 'trends_new_york_city_educational_construction' as table_name, count(*) FROM trends_new_york_city_educational_construction UNION
SELECT 'trends_changes_in_fund_balances' as table_name, count(*) FROM trends_changes_in_fund_balances UNION
SELECT 'trends_capital_assets_statistics_function_program' as table_name, count(*) FROM trends_capital_assets_statistics_function_program UNION
SELECT 'trends_assesed_valuation_tax_rate_class' as table_name, count(*) FROM trends_assesed_valuation_tax_rate_class UNION
SELECT 'trends_assesed_estimated_actual_taxable_property' as table_name, count(*) FROM trends_assesed_estimated_actual_taxable_property UNION
SELECT 'trends_nyc_population' as table_name, count(*) FROM trends_nyc_population UNION
SELECT 'trends_property_tax' as table_name, count(*) FROM trends_property_tax UNION
SELECT 'trends_person_receiving_pubic_assistance' as table_name, count(*) FROM trends_person_receiving_pubic_assistance UNION
SELECT 'trends_pledged_revenue' as table_name, count(*) FROM trends_pledged_revenue UNION
SELECT 'trends_uncollected_parking_violation' as table_name, count(*) FROM trends_uncollected_parking_violation UNION
SELECT 'trends_changes_net_assets' as table_name, count(*) FROM trends_changes_net_assets UNION
SELECT 'trends_government_funds' as table_name, count(*) FROM trends_government_funds UNION
SELECT 'trends_hudson_yards_infrastructure' as table_name, count(*) FROM trends_hudson_yards_infrastructure UNION
SELECT 'trends_legal_debt_margin' as table_name, count(*) FROM trends_legal_debt_margin UNION
SELECT 'trends_ratios_general_bonded_debt_outstanding' as table_name, count(*) FROM  trends_ratios_general_bonded_debt_outstanding

*/
