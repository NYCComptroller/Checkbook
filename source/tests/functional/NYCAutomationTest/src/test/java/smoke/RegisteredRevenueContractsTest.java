package smoke;

import org.junit.Before;

import navigation.TopNavigation.Contracts.RegisteredRevenueContracts;
import pages.contracts.RegisteredRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class RegisteredRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!RegisteredRevenueContracts.isAt())
		   RegisteredRevenueContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
