package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.ContractSpending;
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


//public class MWBEContractSpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBEContractSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){		
	
		//	ContractSpendingPage.GoTo();
		
		MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
		ContractSpendingPage.GoToBottomNavSpendinglink() ;
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count / title /Total amount ****************** */

	
	@Test
	public void VerifyNumOfchecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
	
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEContractSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Contracts Spending Checks  widget Details count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Checks Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Contracts Spending Checks  widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Contracts Spending Checks  widget Details page Total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
	
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEContractSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Contracts Spending Agencies widget Details count did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Contracts Spending Agencies widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Contracts Spending Agencies widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEContractSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Contracts Spending Exp Categories widget Details count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Expense Categories Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Contracts Spending Exp Categories widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Contracts Spending Exp Categories widget Detailspage Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEContractSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Contracts Spending Prime Vendor widget Details count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Contracts Spending Prime Vendor widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Contracts Spending Prime Vendor widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	@Test
	public void VerifyNumOfContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEContractSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Contracts Spending Contracts widget Details count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Contracts Spending Contracts widget Details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEContractsDetailsAmount(year,'B');
			String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
		    assertEquals("M/WBE Contracts Spending Contracts widget Details page Total Spending Amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);

	}
	
	
	
	@Test
	public void VerifyNumOfSubVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5SubVendors);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getMWBEContractSpendingSubVendorsDetailsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Contracts Spending Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Sub Vendors Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	   assertEquals(" M/WBE Contracts Spending Sub Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	   String WidgetDetailsAmountDB =  NYCDatabaseUtil. getContractsSpendingMWBESubVendorsDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Contracts Spending  Sub Vendors  widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	

	@Test
	public void VerifyNumOfSpendingByIndustriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.SpendingByIndustries);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getMWBEContractSpendingDetailsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Industries  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Spending by Industries Contract Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Industries Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getContractsSpendingMWBEDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount2();
	    assertEquals("M/WBE Total Spending  Industries widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
}




