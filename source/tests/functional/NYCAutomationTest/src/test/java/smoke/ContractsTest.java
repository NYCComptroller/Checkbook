package smoke;

import static org.junit.Assert.assertEquals;

import org.junit.Test;

import pages.home.HomePage;
import utilities.NYCBaseTest;

public class ContractsTest extends NYCBaseTest{
	@Test
	public void VerifyActiveExpenseContractsTopMasterAgreementsTableRowsNotEmpty(){
		HomePage.GoTo("http://checkbooknyc.com/contracts_landing/status/A/yeartype/B/year/118/agency/50");
		HomePage.ShowWidgetDetails();
		assertEquals(false, HomePage.IsTableNotEmpty("Top Master Agreements"));
	}
}
