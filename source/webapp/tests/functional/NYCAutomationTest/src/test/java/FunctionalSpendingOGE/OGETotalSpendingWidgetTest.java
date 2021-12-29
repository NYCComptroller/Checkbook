package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCDatabaseUtil;
import utilities.OGENYCBaseTest;
import utilities.OGENYCDatabaseUtil;
import utilities.TestStatusReport;

public class OGETotalSpendingWidgetTest extends OGENYCBaseTest {
	//public class TotalSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	

			@Before
			public void GoToPage(){
				if(!OtherGovernmentEntities.IsAt())
					OtherGovernmentEntities.GoTo();
				if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
					   HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
			
	}

	/* ***************** Test Widget Counts ****************** */
		
		
	
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Total Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfDepartmentsWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingDepartmentsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.TopDepartments);
		assertEquals("Total Spending  Departments widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Total Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Total Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = OGENYCDatabaseUtil.getTotalSpendingContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Total Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}

	
	@Test
    public void VerifyTopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtFY2016 = OGENYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Total Spending Top navigation Amount did not match", spendingAmt, TotalSpendingAmtFY2016);
    }
	
	@Test
    public void VerifyBottomNavTotalSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = OGENYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Total Spending bottom navigation Amount did not match", spendingAmt, TotalSpendingAmtDB);
            
    }
	
	@Test
    public void VerifyTotalSpendingVisualizationsTitles(){
	    String[] sliderTitles= {"Total Spending", 
	    						"Top Ten Contracts by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyTotalSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top Departments",
	    						"Top 5 Expense Categories",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Contracts"
	    						}; 
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    	//HomePage.ShowWidgetDetails();
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	

		}
}


