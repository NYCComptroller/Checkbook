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
public class BudgetWidgetTest extends TestStatusReport{

//public class BudgetWidgetTest extends NYCBaseTest {
	
		@Before
	    public void GoToPage(){
			BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfBudgetAgencies() throws SQLException { 
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPercentDifference);
			assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
	
}

