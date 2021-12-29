package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.CapitalSpending;
import pages.spending.CapitalSpendingPage;
import pages.spending.ContractSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;


//public class MWBECapitalSpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBECapitalSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
	//	if(!MWBEPage.IsAt()){
		MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
		CapitalSpendingPage.GoToBottomNavSpendinglink() ; 
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */
	
	@Test
	public void VerifyNumOfchecksWidgetTransactionPage() throws SQLException {
		
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getMWBECapitalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = CapitalSpendingPage.GetTransactionCount();
		assertEquals("M/WBE Capital Spending checks Widget details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		//Test Widget details page title
		
		String WidgetDetailsTitle =  "M/WBE Checks Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Capital Spending checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Capital Spending checks Widget Details page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB); 
	  	}
	


	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBECapitalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Capital Spending Agecnies Widget Details page total spending amount did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE M/WBE Capital Spending Agencies Widget details page title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Capital Spending Agencies Widget Details page total spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBECapitalSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Capital Spending Exp categories  widget details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Expense Categories Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Capital Spending Exp categories  widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Capital Spending Exp categories  widget details  page total spening amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBECapitalSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Capital Spending Prime Vendor  widget details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Capital Spending Prime Vendor  widget details  title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEDetailsAmount(year,'B');
	    String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		assertEquals("M/WBE Capital Spending Prime Vendor  widget details total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.geMWBECapitalSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Capital Spending  Contracts  widget details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Capital Spending  Contracts  widget details did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEContractsDetailsAmount(year, 'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Capital Spending  Contracts  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
	}
	
	

	
	@Test
	public void VerifyNumOfSpendingByIndustriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.SpendingByIndustries);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getMWBECapitalSpendingDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Industries  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Spending by Industries Capital Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getCapitalContractsSpendingMWBEDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending  Industries widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
}
;



