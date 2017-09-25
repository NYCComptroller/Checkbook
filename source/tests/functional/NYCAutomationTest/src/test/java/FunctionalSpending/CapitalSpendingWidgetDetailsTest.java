package FunctionalSpending;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.CapitalSpending;
import pages.spending.CapitalSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;
import utilities.NYCDatabaseUtilSpending;


public class CapitalSpendingWidgetDetailsTest extends NYCBaseTest {
	//public class TotalSpendingWidgetDetailsTest extends TestStatusReport{

	@Before
	public void GoToPage(){
		
		if (!CapitalSpending.isAt()){
			CapitalSpendingPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}



	/* ***************** Test Widget Transaction Count ****************** */
	
	@Test
	public void VerifyNumOfchecksWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Checks);
		int year = 2016;
		Integer totalCheckswidgetCountFY2016 = NYCDatabaseUtilSpending.getCapitalSpendingDetailsCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Number of Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountFY2016);
	}
/*
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		int year = 2016;
		Integer totalAgencieswidgetCountFY2016 = NYCDatabaseUtil.getCapitalSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Number of agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountFY2016);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
		Integer totalExpenseCategorieswidgetCountFY2016 = NYCDatabaseUtil.getCapitalSpendingDetailsCount(2016,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Number of Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountFY2016);
	}
	
	@Test
	public void VerifyNumOfPrimeVendorsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5PrimeVendors);
		Integer totalPrimeVendorswidgetCountFY2016 = NYCDatabaseUtil.getCapitalSpendingDetailsCount(2016,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Number of Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountFY2016);
	}
	@Test
	public void VerifyNumOfContractsWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Contracts);
		Integer totalContractswidgetCountFY2016 = NYCDatabaseUtil.getCapitalSpendingContractsDetailsCount(2016,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTransactionCount1();
		assertEquals("Number of Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountFY2016);
	}
	//*/
}




