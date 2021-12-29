package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.TrustAgencySpending;
import navigation.MWBECategory.MWBECategoryOption;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.TrustAgencySpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

//public class MWBETrustAgencySpendingWidgetDetailsTest extends NYCBaseTest {
	public class MWBETrustAgencySpendingWidgetDetailsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		
			//TrustAgencySpendingPage.GoTo();

		MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);	
		TrustAgencySpendingPage.GoToBottomNavSpendinglink() ;
		
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */

	@Test
	public void VerifyChecksWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		
		Integer totalChecksWidgetDetailsCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingDetailsCount(year,'B');
		Integer totalChecksWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Trust and Agency Spending  Checks  widget count  did not match with the DB",totalChecksWidgetDetailsCountApp, totalChecksWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Checks Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Trust and Agency spending Checks Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Trust and agency spending Checks widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}

 	@Test
	public void VerifyAgenciesWidgetTransactionPage() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopAgencies);
		
		Integer totalAgenciesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Trust and Agency Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetDetailsCountApp, totalAgenciesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Agencies Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Trust and Agency spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Trust and agency spending Agencies widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyExpenseCategoriesWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopExpenseCategories);
		
		Integer totalExpenseCategoriesWidgetDetailsCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingDetailsCount(2016,'B');
		Integer totalExpenseCategoriesWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Trust and Agency Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetDetailsCountApp, totalExpenseCategoriesWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Expense Categories Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Trust and Agency spending Expense Categories Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Trust and agency spending Expense Categories widget Details  page total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	
	}
	
	
	@Test
	public void VerifyPrimeVendorsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		
		Integer totalPrimeVendorsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingDetailsCount(2016,'B');
		Integer totalPrimeVendorsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Trust and Agency Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetDetailsCountApp, totalPrimeVendorsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Prime Vendors Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Trust and Agency spending Prime Vendors Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp);
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Trust and agency spending Prime Vendors widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	@Test
	public void VerifyContractsWidgetTransactionPage() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		
		Integer totalContractsWidgetDetailsCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingContractsDetailsCount(2016,'B');
		Integer totalContractsWidgetDetailsCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("M/WBE Trust and Agency Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetDetailsCountApp, totalContractsWidgetDetailsCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Contracts Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("M/WBE Trust and Agency spending Contracts Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEContractsDetailsAmount(2016,'B');
		String WidgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("M/WBE Trust and agency spending Contracts widget Details page  total  Spending amount did not match", WidgetDetailsAmountapp, WidgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfSpendingByIndustriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.SpendingByIndustries);
		
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getMWBETrustAgencySpendingDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals(" M/WBE Total Spending Industries  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
		
		String WidgetDetailsTitle =  "M/WBE Spending by Industries Trust & Agency Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals(" M/WBE Total Spending Agencies Widget title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    
	    String WidgetDetailsAmountDB =  NYCDatabaseUtil.getTrustAgencySpendingMWBEDetailsAmount(year, 'B');
		String WidgetDetailsAmountApp = HomePage.GetTransactionAmount1();
	    assertEquals("M/WBE Total Spending  Industries widget details  page total spending amount did not match", WidgetDetailsAmountApp, WidgetDetailsAmountDB);
}
	
}




