package smoke;

import helpers.Helper;

import org.junit.Before;

import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;


public class PayrollTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!PayrollPage.isAt())
		   PayrollPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
