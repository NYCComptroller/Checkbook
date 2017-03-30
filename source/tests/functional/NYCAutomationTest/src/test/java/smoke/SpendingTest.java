package smoke;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.spending.SpendingPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class SpendingTest extends TestStatusReport{
	
	@Before
    public void GoToPage(){
	   if (!SpendingPage.isAt())
		   SpendingPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }

	@Test
    public void VerifySpendingAmount() throws SQLException {
        String TotalSpendingAmtFY2016 = NYCDatabaseUtil.getSpendingAmount(2016, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtFY2016);
    }
	
	@Test
    public void VerifySpendingDomainVisualizationsTitles(){
	    String[] sliderTitles= {"Total Spending", 
	    						"Top Ten Agencies by Disbursement Amount", 
	    						"Top Ten Contracts by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    }
	 
	@Test
    public void VerifySpendingWidgetTitles(){
	    String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Agencies",
	    						"Top 5 Expense Categories",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Contracts"};
	    						//"Top 5 Agencies"};  
    	HomePage.ShowWidgetDetails();
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    }
	
}
