package smoke;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class OGESpendingTest extends TestStatusReport{

	@Before
	public void GoToPage(){
		if(!OtherGovernmentEntities.IsAt())
			OtherGovernmentEntities.GoTo();
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}
	
	@Test
	public void VerifyOGETotalSpendingAmount(){
		String TotalSpendingAmtFY = "$509.1M";
		String totalSpendingAmt = SpendingPage.GetSpendingAmount();
		assertEquals("Spending Amount did not match",TotalSpendingAmtFY, totalSpendingAmt);
	}
}
