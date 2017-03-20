package smoke;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;

public class RevenueTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!RevenuePage.isAt())
		   RevenuePage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
