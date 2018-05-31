package FunctionalSpendingSubVendors;

import static org.junit.Assert.assertEquals;
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


//public class SubVendorsContractSpendingWidgetDetailsTest extends NYCBaseTest {
		public class SubVendorsContractSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	
    public void GoToPage(){
	   if (!SubVendorsPage.IsAt())
			SubVendorsPage.GoTo("Spending", SubVendorCategoryOption.SubVendorsHome);
	   SubVendorsPage.GoToBottomNavSpendinglink() ; 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }



	/* ***************** Test Widget Transaction Count ****************** */
	
	@Test
	public void VerifyNumOfchecksWidgetTransactionPage() throws SQLException {
		
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Contract Spending checks Widget details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		//Test Widget details page title
		
		String WidgetDetailsTitle =  "Sub Vendors Checks Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contract Spending checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Sub Vendors Contract Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  	}
	


	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Contract Spending Agecnies Widget Details page total spending amount did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Agencies Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contract Spending Agencies Widget details page title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Contract Spending Agencies Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifySubVendorsWidgetTransactionPage() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Contract Spending Sub Vendors  widget details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contract Spending Sub Vendors   widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsContractsSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Contract Spending Sub Vendors   widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Contract Spending Prime Vendor  widget details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Prime Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contract Spending Prime Vendor  widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsContractsSpendingDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Contract Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifySubContractsWidgetTransactionPage() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingSubContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Contract Spending  Sub Contracts  widget details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Sub Contracts Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Contract Spending Sub  Contracts  widget details did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsContractsSpendingDetailsAmount(year, 'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("Sub Vendors Contract Spending Sub  Contracts  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	//*/
}
;



