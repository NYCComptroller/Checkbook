DELIMITER $$

CREATE FUNCTION dp_get_next_sequence_id(sequence_name VARCHAR(200), quantity INT)
    RETURNS INT
    MODIFIES SQL DATA
BEGIN
    DECLARE last_used_identifier INT;
    DECLARE updated_record_count INT;

    UPDATE dp_sequences
       SET last_value = LAST_INSERT_ID(last_value + quantity)
     WHERE name = sequence_name;
    SET last_used_identifier = LAST_INSERT_ID();
     
    SELECT ROW_COUNT() INTO updated_record_count;
    IF (updated_record_count = 0) THEN
        BEGIN
          DECLARE record_inserted_in_another_thread INT DEFAULT 0;
          DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' SET record_inserted_in_another_thread = 1;

          INSERT INTO dp_sequences (name, last_value) VALUES (sequence_name, quantity);
          IF (record_inserted_in_another_thread = 0) THEN
              SET last_used_identifier = quantity;
          ELSE
              UPDATE dp_sequences
                 SET last_value = LAST_INSERT_ID(last_value + quantity)
               WHERE name = sequence_name;
              SET last_used_identifier = LAST_INSERT_ID(); 
          END IF;
        END;
    END IF;
     
    RETURN last_used_identifier;
END$$

DELIMITER ;
