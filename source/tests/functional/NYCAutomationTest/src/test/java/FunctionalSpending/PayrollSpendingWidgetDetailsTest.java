package FunctionalSpending;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Spending.PayrollSpending;
import navigation.TopNavigation.Spending.TotalSpending;
import pages.spending.PayrollSpendingPage;
import pages.spending.SpendingPage;
import pages.spending.TotalSpendingPage;
import pages.spending.SpendingPage.WidgetOption;
import pages.home.HomePage;
import pages.payroll.PayrollPage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;
import utilities.NYCDatabaseUtil;

public class PayrollSpendingWidgetDetailsTest extends NYCBaseTest {
	//public class TotalSpendingWidgetDetailsTest extends TestStatusReport{

	@Before
	public void GoToPage(){
		//SpendingPage.GoTo();
		if (!PayrollSpending.isAt()){
			PayrollSpendingPage.GoTo();
		}
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}


	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		int year = 2016;
		Integer totalAgencieswidgetCountFY2016 = NYCDatabaseUtil.getPayrollSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Number of agencies widget count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountFY2016);
	}
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopExpenseCategories);
		Integer totalExpenseCategorieswidgetCountFY2016 = NYCDatabaseUtil.getPayrollSpendingDetailsCount(2016,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Number of Exp categories  widget count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountFY2016);
	}
	

	//*/
}




