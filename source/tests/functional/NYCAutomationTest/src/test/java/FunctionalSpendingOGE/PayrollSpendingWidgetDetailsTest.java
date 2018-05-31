package FunctionalSpendingOGE;

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

//public class PayrollSpendingWidgetDetailsTest extends NYCBaseTest {
public class PayrollSpendingWidgetDetailsTest extends TestStatusReport{
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
	
		PayrollSpendingPage.GoTo();
		
		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}


	/* ***************** Test Widget Transaction Count ****************** */

	
	@Test
	public void VerifyNumOfAgenciesWidgetTransactionCount() throws SQLException {
		SpendingPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
		
		Integer totalAgencieswidgetCountDB = NYCDatabaseUtil.getPayrollSpendingDetailsCount(year,'B');
		Integer totalAgenciesWidgetCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Payroll spending  agencies widget details count  did not match with the DB",totalAgenciesWidgetCountApp, totalAgencieswidgetCountDB);
		
		String AgenciesTitle =  "Agencies Payroll Spending Transactions";
		String SpendingAgenciesTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Payroll spending  Agencies Widget title did not match", AgenciesTitle, SpendingAgenciesTitleApp);
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getPayrollSpendingDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Payroll spending  Agencies widget Details page total spending amount did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB);
	}
	
	@Test
	public void VerifyNumOfExpenseCategoriesWidgetTransactionCount() throws SQLException{
		SpendingPage.GoToTop5DetailsPage(WidgetOption.TopExpenseCategories);
		
		Integer totalExpenseCategorieswidgetCountDB = NYCDatabaseUtil.getPayrollSpendingDetailsCount(year,'B');
		Integer totalExpenseCategoriesWidgetCountApp = SpendingPage.GetTransactionCount();
		assertEquals("Payroll spending Exp categories  widget details count  did not match with the DB",totalExpenseCategoriesWidgetCountApp, totalExpenseCategorieswidgetCountDB);
		
		String WidgetDetailsTitle =  "Expense Categories Payroll Spending Transactions";
		String WidgetDetailsTitleApp = HomePage.DetailsPagetitle();
	    assertEquals("Payroll spending Exp categories  widget details title did not match", WidgetDetailsTitle, WidgetDetailsTitleApp); 
	    
	    String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getPayrollSpendingDetailsAmount(2016,'B');
		String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Payroll spending Exp categories  widget details page total spending amount did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB);
	}
	

	//*/
}




