package FunctionalRevenue;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.revenue.RevenuePage;
import pages.revenue.RevenuePage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class RevenueWidgetTest extends TestStatusReport {


		@Before
	    public void GoToPage(){
			RevenuePage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfRevenueAgenies() throws SQLException {
			int NumOfRevenueAgencies2016 =  NYCDatabaseUtil.getRevenueAgenciesCount(2016,'B');
			int numOfRevenueAgenciesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			assertEquals("Number of Revenue Agencies did not match", numOfRevenueAgenciesapp, NumOfRevenueAgencies2016);
		}
		@Test
		public void VerifyNumOfRevenueCategories() throws SQLException {
			int NumOfRevenueCategories2016 =  NYCDatabaseUtil.getRevenueCategoriesCount(2016,'B');
			int numOfRevenueCategoriesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5RevenueCategories);
	        assertEquals("Number of Revenue categories did not match", numOfRevenueCategoriesapp, NumOfRevenueCategories2016);
		}
		@Test
		public void VerifyNumOfRevenueFundingclass() throws SQLException {
			int NumOfRevenueFundingclass2016 =  NYCDatabaseUtil.getRevenueFundingclassCount(2016,'B');
			int numOfRevenueFundingclassapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.RevenuebyFundingClasses);
	        assertEquals("Number of Revenue funding class did not match", numOfRevenueFundingclassapp, NumOfRevenueFundingclass2016);
		}
}
