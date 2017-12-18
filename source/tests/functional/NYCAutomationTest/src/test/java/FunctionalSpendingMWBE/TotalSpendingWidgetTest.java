package FunctionalSpendingMWBE;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.MWBECategory;
import navigation.MWBECategory.MWBECategoryOption;
import pages.home.HomePage;
import pages.mwbe.MWBEPage;


import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;

import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class TotalSpendingWidgetTest extends NYCBaseTest {
	//public class TotalSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before

	
	public void GoToPage(){
		if(!MWBEPage.IsAt()){
			MWBEPage.GoTo("Spending", MWBECategoryOption.MWBEHome);		
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
	}

	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getTotalSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Total Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Total Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Total Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getTotalSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Total Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Total Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
}




