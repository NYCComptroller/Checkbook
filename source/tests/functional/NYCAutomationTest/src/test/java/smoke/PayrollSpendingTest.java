package smoke;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import helpers.Helper;

import java.io.IOException;
import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.spending.PayrollSpendingPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

//public class PayrollSpendingTest extends TestStatusReport{
	public class PayrollSpendingTest extends NYCBaseTest {
	@Before
    public void GoToPage(){
	   if (!PayrollSpendingPage.isAt()){
		   System.out.println("Inside ISAT");
		   PayrollSpendingPage.GoTo();
	   }
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
    public void VerifyNumOfAgenciesPayrollSpending() {
		Integer PayrollSpendingNumOfAgenciesFY2016 = 130;
        HomePage.ShowWidgetDetails();
        Integer numberOfAgencies = PayrollSpendingPage.GetNumberOfAgencies();
        assertEquals("Number of Agencies in Payroll Spending did not match",PayrollSpendingNumOfAgenciesFY2016, numberOfAgencies);
    }
	
	@Test
    public void IsAtPayrollSpending() {
        assertTrue(PayrollSpendingPage.isAt());
    }
}
