package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.OtherSpending;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.ContractSpendingPage;
import pages.spending.OtherSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

//public class MWBEOtherSpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBEOtherSpendingWidgetDetailsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//SpendingPage.GoTo();
		
			//OtherSpendingPage.GoTo();
			
			MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
			OtherSpendingPage.GoToBottomNavSpendinglink() ;
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifychecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
	
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEOtherSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Other Spending Checks  widget count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Checks Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Other Spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		    assertEquals("M/WBE Other Spending Checks widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEOtherSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Other Spending agencies widget count  did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Other Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingMWBEDetailsAmount(year,'B');
			String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		    assertEquals("M/WBE Other Spending Agencies widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEOtherSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Other Spending Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Expense Categories Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Other Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Other Spending Expense Categories widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBEOtherSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Other Spending Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Other Spending Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingMWBEDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Other Spending Prime Vendors widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	//No widget@Test
	/*public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending Contracts  widget count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
	}*/
	
}




