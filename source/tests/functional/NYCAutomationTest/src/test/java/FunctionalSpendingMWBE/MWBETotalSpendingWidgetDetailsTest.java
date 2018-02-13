package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

//public class MWBETotalSpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBETotalSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);		
		//}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}



	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Total Spending Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Checks Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  

	}
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
	
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Total Spending agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Total Spending Agencies Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEDetailsCount(2016,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Total Spending Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Expense Categories Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Total Spending Exp categories  widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEDetailsCount(2016,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Total Spending Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Total Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEContractsDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending  Contracts  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
	@Test
	public void VerifyNumOfSubVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBESubVendorsDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Sub Vendors widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Sub Vendors Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Sub Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	   String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBESubVendorsDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending  Sub Vendors  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
	@Test
	public void VerifyNumOfSpendingByIndustriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.SpendingByIndustries);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Industries  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Spending by Industries Total Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTotalSpendingMWBEDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending  Industries widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
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
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String AgenciesTitle =  "Agencies Total Spending Transactions";
		String SpendingAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Total Spending Agencies Widget title did not match", AgenciesTitle, SpendingAgenciesTitleApp); 
	}
	*/
}




