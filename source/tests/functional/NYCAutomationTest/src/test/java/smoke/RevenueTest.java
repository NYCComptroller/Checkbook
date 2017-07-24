package smoke;

import helpers.Helper;

import org.junit.Before;

import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class RevenueTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!RevenuePage.isAt())
		   RevenuePage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
