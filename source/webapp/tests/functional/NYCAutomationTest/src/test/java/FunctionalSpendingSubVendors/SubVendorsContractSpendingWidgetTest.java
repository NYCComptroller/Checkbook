package FunctionalSpendingSubVendors;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import java.util.Arrays;
import helpers.Helper;
import java.sql.SQLException;
import org.junit.Before;
import org.junit.Test;

import navigation.SubVendorCategory.SubVendorCategoryOption;
import pages.home.HomePage;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.subvendors.SubVendorsPage;
import pages.subvendors.SubVendorsPage.WidgetOption;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class SubVendorsContractSpendingWidgetTest extends NYCBaseTest {
	public class SubVendorsContractSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	
    public void GoToPage(){
	   if (!SubVendorsPage.IsAt())
			SubVendorsPage.GoTo("Spending", SubVendorCategoryOption.SubVendorsHome);
	 //  navigation.TopNavigation.Spending.ContractSpending.Select();
	  //SubVendorsPage.GoToBottomNavSpendinglink1("Contract Spending") ;
	  SubVendorsPage.GoToBottomNavSpendinglink() ; 
	   
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }


	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Contract Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Contract Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfSubVendorsWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingSubVendorsCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubVendors);
		assertEquals("Contract Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Contract Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingSubContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubContracts);
		assertEquals("Contract Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
	

	@Test
    public void VerifyTopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSubVendorsSpendingAmount(year, 'B');
        String spendingAmt = SubVendorsPage.GetSubVendorSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	
	@Test
    public void VerifyBottomNavContractSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSubVendorsSpendingAmount(year, 'B');
        String spendingAmt = SubVendorsPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
        
     
    }
	
	@Test
    public void VerifyContractSpendingVisualizationsTitles(){
	    String[] sliderTitles= {"Sub Vendors Contract Spending", 
	    		"Top Ten Agencies by Sub Vendors Disbursement Amount", 
				"Top Ten Contracts by Sub Vendors Disbursement Amount", 
				"Top Ten Prime Vendors by Sub Vendors Disbursement Amount",
				"Top Ten Sub Vendors by Disbursement Amount"};
	    System.out.println( SubVendorsPage.VisualizationTitles()); 
    	assertTrue(Arrays.equals(sliderTitles, SubVendorsPage.VisualizationTitles().toArray()));
    	System.out.println( SubVendorsPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifyContractSpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
			   "Top 5 Agencies",
				"Top 5 Sub Vendors",
				"Top 5 Prime Vendors",
				"Top 5 Sub Contracts"}; 
					    					   						
	    							    						 
		   System.out.println( SubVendorsPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, SubVendorsPage.WidgetTitles().toArray()));
    	
     }  
	
}




