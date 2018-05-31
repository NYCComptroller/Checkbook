package FunctionalSpendingOGE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.OtherSpending;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.OtherSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

//public class OtherSpendingWidgetDetailsTest extends NYCBaseTest {
public class OtherSpendingWidgetDetailsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		//SpendingPage.GoTo();
		
			OtherSpendingPage.GoTo();
	
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifychecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
	
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending Checks  widget count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Checks Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Other Spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		    assertEquals("Other Spending Checks widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending agencies widget count  did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Agencies Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Other Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingDetailsAmount(year,'B');
			String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		    assertEquals("Other Spending Agencies widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Other Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Other Spending Expense Categories widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingDetailsCount(year,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Other Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Other Spending Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getOtherSpendingDetailsAmount(year,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Other Spending Prime Vendors widget Details page Total spending Amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	//No widget@Test
	/*public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getOtherSpendingContractsDetailsCount(year,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Other Spending Contracts  widget count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
	}*/
	
}




