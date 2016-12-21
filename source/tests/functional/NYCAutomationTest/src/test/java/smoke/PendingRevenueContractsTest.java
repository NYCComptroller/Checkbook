package smoke;

import org.junit.Before;

import navigation.TopNavigation.Contracts.PendingRevenueContracts;
import pages.contracts.PendingRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class PendingRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!PendingRevenueContracts.isAt())
		   PendingRevenueContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
