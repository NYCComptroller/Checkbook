package smoke;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;


public class PayrollTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!PayrollPage.isAt())
		   PayrollPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
