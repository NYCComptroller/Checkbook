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

//public class SubVendorsTotalSpendingWidgetDetailsTest extends NYCBaseTest {
	public class SubVendorsTotalSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before	
	
    public void GoToPage(){
	  // if (!SubVendorsPage.IsAt())
			SubVendorsPage.GoTo("Spending", SubVendorCategoryOption.SubVendorsHome);
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }



	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Total Spending Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Checks Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Total Spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Sub Vendors Total Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionCount() throws SQLException {
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
	
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Total Spending agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Agencies Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Total Spending Agencies Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfSubVendorsWidgetTransactionCount() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
		Integer totalSubVendorswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalSubVendorsWidgetCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Total Spending Sub Vendors  widget count  did not match with the DB",totalSubVendorsWidgetCountApp, totalSubVendorswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Total Spending Sub Vendors  Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Total Spending Sub Vendors  widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionCount() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals("Sub Vendors Total Spending Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Prime Vendors Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Total Spending Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("Sub Vendors Total Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SubVendorsPage.GoToTop5DetailsPage(WidgetOption.Top5SubContracts);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsCount(year,'B');
		Integer totalContractsWidgetCountApp = SubVendorsPage.GetTransactionCount1();
		assertEquals(" Sub Vendors Total Spending Sub Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "Sub Vendors Sub Contracts Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Sub Vendors Total Spending Sub Contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getSubVendorsTotalSpendingDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("Sub Vendors Total Spending  Sub Contracts widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
	//@Test
	//public void VerifyTop5AgenciesbyPayrollTransactionCount() throws SQLException{
	//	PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		////HomePage.ShowWidgetDetails();
		//Integer NumOfPayrollDetailsCount2016 = NYCDatabaseUtil.getPayrollDetailsCount(2016,'B');
		//Integer numOfPayrollDetailsCountapp = PayrollPage.GetTransactionCount();
		//assertEquals("Total Spending Payroll salaried employees did not match", numOfPayrollDetailsCountapp, NumOfPayrollDetailsCount2016); 
	//}
	/*
	@Test
	public void VerifySpendingTransactionTitle() throws SQLException {
		//Float transactionAmt = 26.3f;
		SubVendorsPage		.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String AgenciesTitle =  "Agencies Total Spending Transactions";
		String SpendingAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Total Spending Agencies Widget title did not match", AgenciesTitle, SpendingAgenciesTitleApp); 
	}
	*/
}




