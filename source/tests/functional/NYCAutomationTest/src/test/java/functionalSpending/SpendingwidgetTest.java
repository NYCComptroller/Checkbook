package functionalSpending;
import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;


	public class SpendingwidgetTest extends NYCBaseTest{

		@Before
	    public void GoToPage(){
			SpendingPage.GoTo();
		   if (!TotalSpending.isAt()){
			   TotalSpendingPage.GoTo();
		   }
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }

		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfchecksWidget() throws SQLException {
		 	int totalCheckswidgetCountFY2016 = NYCDatabaseUtil.getTotalSpendingChecksCount(2016,'B');
	        int totalChecksWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Checks));
	        assertEquals("Number of Checks  widget count  did not match with the DB",totalChecksWidgetCountApp, totalCheckswidgetCountFY2016);
		}

		//Testing with out DB connection
		/*@Test
		public void VerifyNumOfAgenciesWidget(){
		 	int totalAgencieswidgetCountFY2016 = 142;
	        int totalAgenciesWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
	        assertEquals("Number of Checks  widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountFY2016);
		}*/

		@Test
	      public void VerifyNumOfAgenciesWidget() throws SQLException {
		 	int totalAgencieswidgetCountFY2016 = NYCDatabaseUtil.getTotalSpendingAgenciesCount(2016,'B');
	        int totalAgenciesWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
	        assertEquals("Number of agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountFY2016);

		}
		@Test
		public void VerifyNumOfExpenseCategoriesWidget() throws SQLException{
		 	int totalExpenseCategorieswidgetCountFY2016 = NYCDatabaseUtil.getTotalSpendingExpCategoriesCount(2016,'B');
	        int totalExpenseCategoriesWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories));
	        assertEquals("Number of Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountFY2016);
		}
		@Test
		public void VerifyNumOfPrimeVendorsWidget() throws SQLException{
		 	int totalPrimeVendorswidgetCountFY2016 = NYCDatabaseUtil.getTotalSpendingPrimeVendorsCount(2016,'B');
	        int totalPrimeVendorsWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5PrimeVendors));
	        assertEquals("Number of Prime Vendor  widget count  did not match with the DB",totalPrimeVendorsWidgetCountApp, totalPrimeVendorswidgetCountFY2016);
		}
		@Test
	    	public void VerifyNumOfContractsWidget() throws SQLException{
	    	 	int totalContractswidgetCountFY2016 = NYCDatabaseUtil.getTotalSpendingContractsCount(2016,'B');
	            int totalContractsWidgetCountApp = Helper.stringToInt(SpendingPage.GetTop5WidgetTotalCount(WidgetOption.Top5Contracts));
	            assertEquals("Number of Contracts  widget count  did not match with the DB",totalContractsWidgetCountApp, totalContractswidgetCountFY2016);


		}

	}




