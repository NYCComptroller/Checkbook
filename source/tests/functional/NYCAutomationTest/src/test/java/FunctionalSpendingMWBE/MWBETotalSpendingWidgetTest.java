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

public class MWBETotalSpendingWidgetTest extends NYCBaseTest {
		//public class MWBETotalSpendingWidgetTest extends TestStatusReport{
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
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Total Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Total Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEExpCategoriesCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
		assertEquals("Total Spending  Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
	}
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Total Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts);
		assertEquals("Total Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
	
	@Test
	public void VerifyNumOfSubVendorsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBESubVendorsCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubVendors);
		assertEquals("Total Spending  SubVendor widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
	@Test
	public void VerifyNumOfSpendingByIndustriesWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getTotalSpendingMWBEIndustriesCount(year,'B');
		Integer totalContractsWidgetCountApp = SpendingPage.GetTop5WidgetTotalCount(WidgetOption.SpendingByIndustries);
		assertEquals("Total Spending  Industries  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
}




