package FunctionalSpending;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class ContractSpendingWidgetTest extends NYCBaseTest {
	public class ContractSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		if (!ContractSpendingPage.isAt()){
			ContractSpendingPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getContractSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Contract Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getContractSpendingAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Contract Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getContractSpendingExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Contract Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getContractSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Contract Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getContractSpendingContractsCount(year,'B');
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
    public void VerifySpendingDomainVisualizationsTitles(){
	    String[] sliderTitles= {"Contract Spending", 
	    						"Top Ten Agencies by Disbursement Amount", 
	    						"Top Ten Contracts by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifySpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Agencies",
	    						"Top 5 Expense Categories",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Contracts"};	    						
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	
     } 
}



