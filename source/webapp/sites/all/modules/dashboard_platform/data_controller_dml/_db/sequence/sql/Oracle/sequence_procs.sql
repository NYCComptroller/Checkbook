CREATE OR REPLACE FUNCTION dp_get_next_sequence_id(sequence_name IN dp_sequences.name%TYPE, quantity IN dp_sequences.last_value%TYPE)
    RETURN dp_sequences.last_value%TYPE
IS
    PRAGMA AUTONOMOUS_TRANSACTION;

    v_last_used_identifier  dp_sequences.last_value%TYPE;
    v_updated_record_count  NUMBER(10);
BEGIN
    UPDATE dp_sequences
       SET last_value = last_value + quantity
     WHERE name = sequence_name
    RETURNING last_value INTO v_last_used_identifier;

    v_updated_record_count = SQL%ROWCOUNT;
    IF (v_updated_record_count = 0) THEN
        BEGIN
            INSERT INTO dp_sequences (name, last_value) VALUES (sequence_name, quantity);
            v_last_used_identifier := quantity;
        EXCEPTION
            -- other thread meanwhile inserted required record
            WHEN DUP_VAL_ON_INDEX THEN
                UPDATE dp_sequences
                   SET last_value = last_value + quantity
                 WHERE name = sequence_name
                RETURNING last_value INTO v_last_used_identifier;
                IF (SQL%ROWCOUNT != 1) THEN
                    RAISE_APPLICATION_ERROR(-20399, 'Could not obtain value for "' || sequence_name || '" sequence');
                END IF;
        END;
        COMMIT;
    ELSEIF (v_updated_record_count = 1)
        COMMIT;
    ELSE
        ROLLBACK;
    END IF;

    RETURN v_last_used_identifier;
END dp_get_next_sequence_id;
