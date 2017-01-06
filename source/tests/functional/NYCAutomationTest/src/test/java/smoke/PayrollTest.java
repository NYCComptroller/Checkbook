package smoke;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utility.Helper;

public class PayrollTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!PayrollPage.isAt())
		   PayrollPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
