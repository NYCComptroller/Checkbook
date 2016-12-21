package smoke;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Contracts.ActiveRevenueContracts;
import pages.contracts.ActiveRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ActiveRevenueContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!ActiveRevenueContracts.isAt()){
		   System.out.print("ActiveRevenueContractsTest");
		   ActiveRevenueContractsPage.GoTo();
	   }
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	@Test
	public void VerifyTop5MasterAgreements(){
		
	}
}
