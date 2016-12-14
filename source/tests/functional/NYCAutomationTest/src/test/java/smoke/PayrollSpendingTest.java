package smoke;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.io.IOException;
import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.spending.PayrollSpendingPage;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;

public class PayrollSpendingTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!PayrollSpendingPage.isAt())
		   PayrollSpendingPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
    public void VerifyNumOfAgenciesPayrollSpending() {
        String PayrollSpendingNumOfAgenciesFY2016 = "130";
        String numberOfAgencies = PayrollSpendingPage.GetTotalNumOfAgencies();
        assertEquals("Number of Agencies in Payroll Spending did not match", numberOfAgencies, PayrollSpendingNumOfAgenciesFY2016);
    }
	
	@Test
    public void IsAtPayrollSpending() {
        assertTrue(PayrollSpendingPage.isAt());
    }
}
