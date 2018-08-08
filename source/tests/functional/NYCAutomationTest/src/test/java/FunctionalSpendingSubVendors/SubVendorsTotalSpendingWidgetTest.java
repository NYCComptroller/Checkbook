package FunctionalSpendingSubVendors;

import static org.junit.Assert.assertEquals;
import helpers.Helper;
import java.sql.SQLException;
import org.junit.Before;
import org.junit.Test;

import navigation.SubVendorCategory.SubVendorCategoryOption;
import pages.home.HomePage;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.subvendors.SubVendorsPage;
import pages.subvendors.SubVendorsPage.WidgetOption;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class SubVendorsTotalSpendingWidgetTest extends NYCBaseTest {
	public class SubVendorsTotalSpendingWidgetTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before

	
    public void GoToPage(){
	   if (!SubVendorsPage.IsAt())
			SubVendorsPage.GoTo("Spending", SubVendorCategoryOption.SubVendorsHome);
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }


	/* ***************** Test Widget Counts ****************** */
	@Test
	public void VerifyNumOfchecksWidget() throws SQLException {
		Integer totalCheckswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingChecksCount(year,'B');
		Integer totalChecksWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks);
		assertEquals("Total Spending  Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountDB);
	}
	@Test
	public void VerifyNumOfAgenciesWidget() throws SQLException {
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingAgenciesCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
		assertEquals("Total Spending  agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
	}
	
	@Test
	public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingPrimeVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors);
		assertEquals("Total Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	

	@Test
	public void VerifyNumOfSubVendorsWidget() throws SQLException{
		Integer totalPrimeVendorswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingSubVendorsCount(year,'B');
		Integer totalPrimeVendorsWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubVendors);
		assertEquals("Total Spending  Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountDB);
	}
	@Test
	public void VerifyNumOfContractsWidget() throws SQLException{
		Integer totalContractswidgetCountDB = NYCDatabaseUtil.getSubVendorsTotalSpendingSubContractsCount(year,'B');
		Integer totalContractsWidgetCountApp = SubVendorsPage.GetTop5WidgetTotalCount(WidgetOption.Top5SubContracts);
		assertEquals("Total Spending  Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountDB);
	}
}




