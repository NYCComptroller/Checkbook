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
	
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
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
			Integer NumOfRevenueAgenciesDB =  NYCDatabaseUtil.getRevenueAgenciesCount(year,'B');
			Integer numOfRevenueAgenciesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			assertEquals("Revenue domain  Agencies widget count did not match with DB", numOfRevenueAgenciesapp, NumOfRevenueAgenciesDB);
		}

		@Test
		public void VerifyNumOfRevenueCategories() throws SQLException {
			Integer NumOfRevenueCategoriesDB =  NYCDatabaseUtil.getRevenueCategoriesCount(year,'B');
			Integer numOfRevenueCategoriesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5RevenueCategories);
	        assertEquals("Revenue domain Revenue categories widget count did not match with DB", numOfRevenueCategoriesapp, NumOfRevenueCategoriesDB);
		}
		@Test
		public void VerifyNumOfRevenueFundingclass() throws SQLException {
			Integer NumOfRevenueFundingclassDB =  NYCDatabaseUtil.getRevenueFundingclassCount(year,'B');
			Integer numOfRevenueFundingclassapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.RevenuebyFundingClasses);
	        assertEquals("Revenue domain  funding class widget count did not match with DB", numOfRevenueFundingclassapp, NumOfRevenueFundingclassDB);
		}
		
		@Test
		public void VerifyNumOfRevenueAgeniesCrossYearCollections() throws SQLException {
			Integer NumOfRevenueAgenciesDB =  NYCDatabaseUtil.getRevenueAgenciesCrossYearColectionsDetailsCount(year,'B');
			Integer numOfRevenueAgenciesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyCrossYearCollections);
			assertEquals("Revenue domain Agencies byCrossYearCollections widget count did not match with DB", numOfRevenueAgenciesapp, NumOfRevenueAgenciesDB);
		}

		@Test
		public void VerifyNumOfRevenueCategoriesCrossYearCollections() throws SQLException {
			Integer NumOfRevenueCategoriesDB =  NYCDatabaseUtil.getRevenueCategoriesCrossYearColectionsDetailsCount(year,'B');
			Integer numOfRevenueCategoriesapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.Top5RevenueCategoriesbyCrossYearCollections);
	        assertEquals("Revenue domain Revenue categories byCrossYearCollections widget count did not match with DB", numOfRevenueCategoriesapp, NumOfRevenueCategoriesDB);
		}
		@Test
		public void VerifyNumOfRevenueFundingclassCrossYearCollections() throws SQLException {
			Integer NumOfRevenueFundingclassDB =  NYCDatabaseUtil.getRevenueFundingClassesCrossYearColectionsDetailsCount(year,'B');
			Integer numOfRevenueFundingclassapp = RevenuePage.GetTop5WidgetTotalCount(WidgetOption.RevenuebyFundingClassesbyCrossYearCollections);
	        assertEquals("Revenue domain funding classbyCrossYearCollections widget count did not match with DB", numOfRevenueFundingclassapp, NumOfRevenueFundingclassDB);
		}

}
