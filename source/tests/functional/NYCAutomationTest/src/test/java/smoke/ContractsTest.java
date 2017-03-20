package smoke;

import static org.junit.Assert.assertEquals;

import org.junit.Before;
import org.junit.Test;

import pages.contracts.ContractsPage;
import pages.home.HomePage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utility.Helper;

public class ContractsTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!ContractsPage.isAt())
		   SpendingPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	@Test
	public void VerifyActiveExpenseContractsTopMasterAgreementsTableRowsNotEmpty(){
		HomePage.GoTo("http://checkbooknyc.com/contracts_landing/status/A/yeartype/B/year/118/agency/50");
		HomePage.ShowWidgetDetails();
		assertEquals(false, HomePage.IsTableNotEmpty("Top Master Agreements"));
	}
}
