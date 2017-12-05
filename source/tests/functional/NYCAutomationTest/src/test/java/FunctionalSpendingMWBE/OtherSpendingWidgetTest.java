package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.OtherSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class OtherSpendingWidgetTest extends NYCBaseTest {
	public class OtherSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
	
		if (!OtherSpendingPage.isAt()){
			OtherSpendingPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getOtherSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Other Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getOtherSpendingAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Other Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getOtherSpendingExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Other Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getOtherSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Other Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	
	@Test
    public void VerifyTopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	
	@Test
    public void VerifyBottomNavOtherSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getOtherSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
            
    }
	
	@Test
    public void VerifyOtherSpendingVisualizationsTitles(){
	    String[] sliderTitles= {"Other Spending", 
	    						"Top Ten Agencies by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyOtherSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Agencies",
	    						"Top 5 Expense Categories",
	    						"Top 5 Prime Vendors"};
	    						   						
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	
     }  

}




