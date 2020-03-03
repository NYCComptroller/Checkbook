CREATE TABLE dp_day_of_week_def (
       day_of_week_def_id   NUMBER(10) NOT NULL,
       series               NUMBER(10) NOT NULL,
       code                 CHAR(3) NOT NULL,
       name                 VARCHAR2(20) NOT NULL,
       CONSTRAINT pk_dp_day_of_week_def PRIMARY KEY (day_of_week_def_id),
       CONSTRAINT uk_dp_day_of_week_def_series UNIQUE (series),
       CONSTRAINT uk_dp_day_of_week_def_code UNIQUE (code)
);


CREATE TABLE dp_years (
       year_id              NUMBER(10) NOT NULL,
       entry_year           NUMBER(10) NOT NULL,
       CONSTRAINT pk_dp_years PRIMARY KEY (year_id),
       CONSTRAINT uk_dp_years UNIQUE (entry_year)
);


CREATE TABLE dp_month_def (
       month_def_id         NUMBER(10) NOT NULL,
       series               NUMBER(10) NOT NULL,
       code                 CHAR(3) NOT NULL,
       name                 VARCHAR2(20) NOT NULL,
       CONSTRAINT pk_dp_month_def PRIMARY KEY (month_def_id),
       CONSTRAINT uk_dp_month_def_series UNIQUE (series),
       CONSTRAINT uk_dp_month_def_code UNIQUE (code)
);


CREATE TABLE dp_months (
       month_id             NUMBER(10) NOT NULL,
       month_def_id         NUMBER(10) NOT NULL,
       year_id              NUMBER(10) NOT NULL,
       CONSTRAINT pk_dp_months PRIMARY KEY (month_id), 
       CONSTRAINT fk_dp_months_years FOREIGN KEY (year_id) REFERENCES dp_years (year_id), 
       CONSTRAINT fk_dp_months_month_def FOREIGN KEY (month_def_id) REFERENCES dp_month_def (month_def_id),
       CONSTRAINT uk_dp_months UNIQUE (year_id, month_def_id)
);


CREATE TABLE dp_dates (
       date_id              NUMBER(10) NOT NULL,
       entry_date           DATE NOT NULL,
       day_of_week_def_id   NUMBER(10) NOT NULL,
       month_id             NUMBER(10) NOT NULL,
       CONSTRAINT pk_dp_dates PRIMARY KEY (date_id), 
       CONSTRAINT fk_dp_days_day_of_week_def FOREIGN KEY (day_of_week_def_id) REFERENCES dp_day_of_week_def (day_of_week_def_id), 
       CONSTRAINT fk_dp_days_months FOREIGN KEY (month_id) REFERENCES dp_months (month_id),
       CONSTRAINT uk_dp_dates UNIQUE (entry_date)
);


CREATE TABLE dp_quarter_def (
       quarter_def_id       NUMBER(10) NOT NULL,
       series               NUMBER(10) NOT NULL,
       code                 VARCHAR2(3) NOT NULL,
       name                 VARCHAR2(20) NOT NULL,
       CONSTRAINT pk_dp_quarter_def PRIMARY KEY (quarter_def_id),
       CONSTRAINT uk_dp_quarter_def_series UNIQUE (series),
       CONSTRAINT uk_dp_quarter_def_code UNIQUE (code)
);


CREATE TABLE dp_quarters (
       quarter_id           NUMBER(10) NOT NULL,
       quarter_def_id       NUMBER(10) NOT NULL,
       year_id              NUMBER(10) NOT NULL,
       CONSTRAINT pk_dp_quarters PRIMARY KEY (quarter_id), 
       CONSTRAINT fk_dp_quarters_years FOREIGN KEY (year_id) REFERENCES dp_years (year_id), 
       CONSTRAINT fk_dp_quarters_quarter_def FOREIGN KEY (quarter_def_id) REFERENCES dp_quarter_def (quarter_def_id),
       CONSTRAINT uk_dp_quarters UNIQUE (year_id, quarter_def_id)
);
