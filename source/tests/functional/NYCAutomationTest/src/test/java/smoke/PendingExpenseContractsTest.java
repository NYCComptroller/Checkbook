package smoke;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.PendingExpenseContracts;
import pages.contracts.PendingExpenseContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class PendingExpenseContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!PendingExpenseContracts.isAt())
		   PendingExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
	public void VerifyTop5MasterAgreements(){
		
	}
}
