package FunctionalBudget;

import static org.junit.Assert.assertEquals;
import helpers.Helper;
import java.sql.SQLException;
import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

import static org.junit.Assert.assertTrue;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import helpers.Driver;


public class BudgetWidgetCountsTest extends TestStatusReport{

	//public class BudgetWidgetCountsTest  extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
		@Before
	    public void GoToPage(){
			 if (!BudgetPage.isAt())
				   BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfBudgetAgencies() throws SQLException { 
			Integer NumOfBudgetAgenciesDB =  NYCDatabaseUtil.getBudgetAgenciesCount(year,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			assertEquals(" Budget Domain Agenices widget count did not match with DB", numOfBudgetAgenciesapp, NumOfBudgetAgenciesDB);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetAgenciesDB =  NYCDatabaseUtil.getBudgetAgenciesCount(year,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			assertEquals("Budget Domain Agenices by Committed Expense Budget widget count did not match with DB", numOfBudgetAgenciesapp, NumOfBudgetAgenciesDB);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetAgenciesDB =  NYCDatabaseUtil.getBudgetAgenciesCount(year,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPercentDifference);
			assertEquals(" Budget Domain Agenices by Percent Difference widget count did not match with DB", numOfBudgetAgenciesapp, NumOfBudgetAgenciesDB);
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			Integer NumOfBudgetExpenseCategoriesDB =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(year,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
	        assertEquals(" Budget Domain Expense Categories widget count did not match with DB", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategoriesDB);
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetExpenseCategoriesDB =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(year,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
	        assertEquals("Budget Domain Expense Categories by Committed Expense Budget widget count did not match with DB", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategoriesDB);
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetExpenseCategoriesDB =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(year,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
	        assertEquals("Budget Domain Expense Categories by Percent Difference widget count did not match with DB", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategoriesDB);
		}
	
}

