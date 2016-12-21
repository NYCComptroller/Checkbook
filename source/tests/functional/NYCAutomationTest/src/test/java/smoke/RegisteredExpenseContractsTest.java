package smoke;

import org.junit.Before;

import navigation.TopNavigation.Contracts.RegisteredExpenseContracts;
import pages.contracts.RegisteredExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class RegisteredExpenseContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!RegisteredExpenseContracts.isAt())
		   RegisteredExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
}
