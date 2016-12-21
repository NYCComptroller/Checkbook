package smoke;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.contracts.ActiveExpenseContractsPage;
import pages.contracts.ContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ActiveExpenseContractsTest extends NYCBaseTest{

	@Before
    public void GoToPage(){
		ContractsPage.GoTo();
	   if (!ActiveExpenseContracts.isAt())
		   ActiveExpenseContractsPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
	@Test
	public void VerifyTop5MasterAgreements(){
		
	}
}
