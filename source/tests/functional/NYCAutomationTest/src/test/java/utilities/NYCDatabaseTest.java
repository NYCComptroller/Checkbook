package utilities;

import static org.junit.Assert.assertEquals;

import java.math.BigDecimal;
import java.sql.SQLException;

import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.Test;

/**
 * Created by thomas.le on 11/10/2016.
 * Just for testing purposes. Disregard.
 */
public class NYCDatabaseTest {

    @BeforeClass
    public static void connectToDB() throws SQLException, ClassNotFoundException {
        NYCDatabaseUtil.connectToDatabase();
    }

    @AfterClass
    public static void closeConnectionToDB() throws SQLException {
        NYCDatabaseUtil.closeDatabase();
    }

    @Test
    public void getSpendingAmountValidation() throws SQLException {
        BigDecimal expected2016Amt = new BigDecimal(94933169128.85);

        System.out.println("Expected amount: " +
                expected2016Amt.divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP));
        System.out.println("Actual amount: " +
                NYCDatabaseUtil.getSpendingAmount(2016, 'B'));

        assertEquals(expected2016Amt.divide(new BigDecimal(1000000000)).setScale(1, BigDecimal.ROUND_HALF_UP),
                NYCDatabaseUtil.getSpendingAmount(2016, 'B'));
    }
}
