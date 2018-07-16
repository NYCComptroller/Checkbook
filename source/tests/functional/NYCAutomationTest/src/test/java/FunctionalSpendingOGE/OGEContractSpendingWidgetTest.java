package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import helpers.Helper;
import navigation.SecondaryMenuNavigation.OtherGovernmentEntities;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

//import navigation.TopNavigation.Spending.TotalSpending;
import pages.spendingoge.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;

import utilities.OGENYCDatabaseUtil;
import utilities.OGENYCBaseTest;
import utilities.TestStatusReport;

public class OGEContractSpendingWidgetTest extends OGENYCBaseTest {
	//public class ContractSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(OGENYCBaseTest.prop.getProperty("year"));
	@Before
	
		public void GoToPage(){
			//if(!OtherGovernmentEntities.IsAt())
				OtherGovernmentEntities.GoTo();
			ContractSpendingPage.GoToBottomNavSpendinglink() ; 
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(OGENYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(OGENYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = OGENYCDatabaseUtil.getContractSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Contract Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfDepartmentsWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = OGENYCDatabaseUtil.getContractSpendingDepartmentsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.TopDepartments);
		assertEquals("Contract Spending  Departments widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = OGENYCDatabaseUtil.getContractSpendingExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.TopExpenseCategories);
		assertEquals("Contract Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = OGENYCDatabaseUtil.getContractSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.TopPrimeVendors);
		assertEquals("Contract Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = OGENYCDatabaseUtil.getContractSpendingContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Contract Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
	
	@Test
    public void VerifySpendingAmount() throws SQLException {
        String TotalSpendingAmtDB = OGENYCDatabaseUtil.getSpendingAmount(year, 'B');
        String spendingAmt = SpendingPage.GetSpendingAmount();
        assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtDB);
    }
	
	@Test
    public void VerifySpendingDomainVisualizationsTitles(){
	    String[] sliderTitles= {"Contract Spending", 
	    						"Top Ten Contracts by Disbursement Amount", 
	    						"Top Ten Prime Vendors by Disbursement Amount"};  
    	assertTrue(Arrays.equals(sliderTitles, SpendingPage.VisualizationTitles().toArray()));
    	System.out.println( SpendingPage.VisualizationTitles()); 
    }
	 
	@Test
    public void VerifySpendingWidgetTitles(){
	   String[] widgetTitles = {"Top 5 Checks",
	    						"Top 5 Departments",
	    						"Top 5 Expense Categories",
	    						"Top 5 Prime Vendors",
	    						"Top 5 Contracts"};	    						
	    							    						 
		   System.out.println( SpendingPage.WidgetTitles()); 		
    
    	assertTrue(Arrays.equals(widgetTitles, SpendingPage.WidgetTitles().toArray()));
    	
     } 
}



