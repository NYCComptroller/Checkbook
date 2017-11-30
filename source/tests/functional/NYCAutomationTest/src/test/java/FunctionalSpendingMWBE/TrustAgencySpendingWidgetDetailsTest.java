package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.TrustAgencySpending;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.TrustAgencySpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

//public class TrustAgencySpendingWidgetDetailsTest extends NYCBaseTest {
	public class TrustAgencySpendingWidgetDetailsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		
			TrustAgencySpendingPage.GoTo();
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */

	@Test
	public void VerifyChecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getTrustAgencySpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Trust and Agency Spending  Checks  widget count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Checks Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Trust and Agency spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Trust and agency spending Checks widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}

 	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getTrustAgencySpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Trust and Agency Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Agencies Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Trust and Agency spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Trust and agency spending Agencies widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getTrustAgencySpendingDetailsCount(2016,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Trust and Agency Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Trust and Agency spending Expense Categories Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Trust and agency spending Expense Categories widget Details  page total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	
	}
	
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getTrustAgencySpendingDetailsCount(2016,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Trust and Agency Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Prime Vendors Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Trust and Agency spending Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Trust and agency spending Prime Vendors widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getTrustAgencySpendingContractsDetailsCount(2016,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Trust and Agency Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "Contracts Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Trust and Agency spending Contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Trust and agency spending Contracts widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
}




