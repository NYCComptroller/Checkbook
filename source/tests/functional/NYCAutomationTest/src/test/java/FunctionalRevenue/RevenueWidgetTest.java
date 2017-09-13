package FunctionalRevenue;

import static org.junit.Assert.assertEquals;
import java.sql.SQLException;
import org.junit.Before;
import org.junit.Test;
import pages.revenue.RevenuePage;
import pages.revenue.RevenuePage.WidgetOption;
import pages.home.HomePage;
import helpers.Helper;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

//public class RevenueWidgetTest extends NYCBaseTest {
	public class RevenueWidgetTest extends TestStatusReport {
	

		@Before
	    public void GoToPage() {
			RevenuePage.GoTo();
		 
			if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
				HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
			HomePage.ShowWidgetDetails();
		}
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfRevenueAgenies() throws SQLException {
			Integer NumOfRevenueAgencies2016 =  NYCDatabaseUtil.getRevenueAgenciesCount(2016,'B');
			Integer numOfRevenueAgenciesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			assertEquals("Number of Revenue Agencies did not match", numOfRevenueAgenciesapp, NumOfRevenueAgencies2016);
		}

		public void VerifyNumOfRevenueAgenies1() {
			Integer NumOfRevenueAgencies2016 =  150;
			Integer numOfRevenueAgenciesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			 assertEquals("Number of Revenue Agencies did not match", numOfRevenueAgenciesapp, NumOfRevenueAgencies2016);
		}
		@Test
		public void VerifyNumOfRevenueCategories() throws SQLException {
			Integer NumOfRevenueCategories2016 =  NYCDatabaseUtil.getRevenueCategoriesCount(2016,'B');
			Integer numOfRevenueCategoriesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5RevenueCategories);
	        assertEquals("Number of Revenue categories did not match", numOfRevenueCategoriesapp, NumOfRevenueCategories2016);
		}
		@Test
		public void VerifyNumOfRevenueFundingclass() throws SQLException {
			Integer NumOfRevenueFundingclass2016 =  NYCDatabaseUtil.getRevenueFundingclassCount(2016,'B');
			Integer numOfRevenueFundingclassapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.RevenuebyFundingClasses);
	        assertEquals("Number of Revenue funding class did not match", numOfRevenueFundingclassapp, NumOfRevenueFundingclass2016);
		}

}
