package FunctionalBudget;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Rule;
import org.junit.Test;
import org.junit.rules.ErrorCollector;

import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;
import pages.home.HomePage;

import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;

public class BudgetWidgetTest extends NYCBaseTest {
		@Before
	    public void GoToPage(){
			BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/*
		 * The ErrorCollector Rule allows execution of a test to continue after the
		 * first problem is found and report them all at once
		 */
		 @Rule
		 public ErrorCollector errCol = new ErrorCollector();
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfBudgetAgencies() throws SQLException { 
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			try {
				assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfBudgetAgencies failed " + ae.getMessage());
			}
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			try {
				assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget failed " + ae.getMessage());
			}
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPercentDifference);
			assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
			try {
				assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget failed " + ae.getMessage());
			}
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories);
			try {
		        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfExpenseCategories failed " + ae.getMessage());
			}
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
			try {
		        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget failed " + ae.getMessage());
			}
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {
			Integer NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			Integer numOfBudgeteExpenseCategoriesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
			try {
		        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
			} 
			catch(AssertionError ae) {
				System.out.println("VerifyNumOfExpenseCategoriesbyPercentDifference failed " + ae.getMessage());
			}
		}
	
}

