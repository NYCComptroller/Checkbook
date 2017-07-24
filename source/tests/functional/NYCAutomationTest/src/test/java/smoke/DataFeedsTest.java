package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.*;

import navigation.PrimaryMenuNavigation;
import pages.datafeeds.DataFeedsPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class DataFeedsTest extends TestStatusReport{
	@Before
	public void GoToPage(){
		if(!DataFeedsPage.isAt()){
			System.out.println("Inside GOTO");
			DataFeedsPage.GoTo();
		}
	}

	@Test
    public void dataFeedsToThankYouPage() {
        DataFeedsPage.submitDataFeedsForm();
        assertTrue(PrimaryMenuNavigation.isAt("Thank You"));
    }
}
