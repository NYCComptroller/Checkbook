package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.CapitalSpendingPage;
import pages.spending.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class MWBEContractSpendingWidgetTest extends NYCBaseTest {
	public class MWBEContractSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//if (!ContractSpendingPage.isAt()){
		//	ContractSpendingPage.GoTo();}
		
		MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
		ContractSpendingPage.GoToBottomNavSpendinglink() ;
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getContractSpendingMWBEChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Contract Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getContractSpendingMWBEAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Contract Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getContractSpendingMWBEExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Contract Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getContractSpendingMWBEPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Contract Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getContractSpendingMWBEContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Contract Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
	
	@Test
    public void VerifySpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }	
	

	@Test
    public void VerifyMWBETopNavSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getSpendingMWBEAmount(year, 'B');
        String spendingAmt = MWBEPage.GetMWBEAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	@Test
    public void VerifyMWBEBottomNavContractSpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = NYCDatabaseUtil.getContractSpendingMWBEAmount(year, 'B');
        String spendingAmt = SpendingPage.GetBottomNavSpendingAmount();
    	System.out.println(spendingAmt); 
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
       
     
    }
	@Test
    public void VerifySpendingDomainVisualizationsTitles(){
	   
	    String[] sliderTitles= {"Prime Spending by M/WBE Share",
	    		"M/WBE Contract Prime Spending Share",
	    		"Analysis by Prime M/WBE Share",
	    		"Top Ten Agencies by M/WBE Spending",
	    		"Top Ten Prime Vendors by M/WBE Spending",
	    		"Top Ten Contracts by M/WBE Spending",
	    		"Top Ten Sub Vendors by M/WBE Spending"
	    					}; 
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifySpendingWidgetTitles(){
		   String[] widgetTitles = {"Top 5 Checks",
					"Top 5 Agencies",
					"Top 5 Expense Categories",
					"Top 5 Prime Vendors",
					"Top 5 Sub Vendors",
					"Top 5 Contracts",
					"Spending by Industries"};    						
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	
     } 
}



