CREATE TABLE dp_sequences(
       name         VARCHAR(200) NOT NULL,
       last_value   INT NOT NULL,

       CONSTRAINT pk_dp_sequences PRIMARY KEY(name)
)
ENGINE = InnoDB;
