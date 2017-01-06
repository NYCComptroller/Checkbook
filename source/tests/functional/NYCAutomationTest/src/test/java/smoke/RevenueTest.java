package smoke;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.revenue.RevenuePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class RevenueTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!RevenuePage.isAt())
		   RevenuePage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
