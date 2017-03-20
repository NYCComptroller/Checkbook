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
	   if (!PayrollSpendingPage.isAt()){
		   System.out.println("Inside ISAT");
		   PayrollSpendingPage.GoTo();
	   }
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
    public void VerifyNumOfAgenciesPayrollSpending() {
        int PayrollSpendingNumOfAgenciesFY2016 = 130;
        HomePage.ShowWidgetDetails();
        int numberOfAgencies = Helper.stringToInt(PayrollSpendingPage.GetNumberOfAgencies());
        assertEquals("Number of Agencies in Payroll Spending did not match",PayrollSpendingNumOfAgenciesFY2016, numberOfAgencies);
    }
	
	@Test
    public void IsAtPayrollSpending() {
        assertTrue(PayrollSpendingPage.isAt());
    }
}
