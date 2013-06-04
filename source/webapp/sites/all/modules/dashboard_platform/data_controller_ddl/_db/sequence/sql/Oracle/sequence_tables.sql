CREATE TABLE dp_sequences(
       name         DBA_TABLES.TABLE_NAME%TYPE NOT NULL,
       last_value   NUMBER(10) NOT NULL,

       CONSTRAINT pk_dp_sequences PRIMARY KEY(name)
);
