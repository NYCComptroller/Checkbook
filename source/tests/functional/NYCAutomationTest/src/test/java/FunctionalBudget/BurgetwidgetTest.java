package FunctionalBudget;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Budget;
//import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;
//import pages.contracts.ActiveExpenseContractsPage;
//import pages.contracts.ContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;
import utility.TestStatusReport;

public class BurgetwidgetTest   extends NYCBaseTest{
	//public class BurgetwidgetTest   extends TestStatusReport{


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
		 	int NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
	       int numOfBudgetAgenciesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies));
		        assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudget() throws SQLException {
		 	int NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
	       int numOfBudgetAgenciesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyCommittedExpenseBudget));
		        assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
		 	int NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
	       int numOfBudgetAgenciesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPercentDifference));
		        assertEquals("Number of Budget Agenies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
		 	int NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			//int NumOfBudgetExpenseCategories2016 = 130;
	        int numOfBudgeteExpenseCategoriesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategories));
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
		 	int NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			//int NumOfBudgetExpenseCategories2016 = 130;
	        int numOfBudgeteExpenseCategoriesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget));
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
	
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {
			int NumOfBudgetExpenseCategories2016 =  NYCDatabaseUtil.getBudgetExpenseCategoriesCount(2016,'B');
			 int numOfBudgeteExpenseCategoriesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseCategoriesbyPercentDifference));
	        assertEquals("Number of Budget Expense Categories did not match", numOfBudgeteExpenseCategoriesapp, NumOfBudgetExpenseCategories2016);
		}
/*
		@Test
		public void VerifyNumOfExpenseBudgetCategories() throws SQLException {
		 	int NumOfExpenseBudgetCategories2016 =  NYCDatabaseUtil.getBudgetExpenseBudgetCategoriesCount(2016,'B');
	        int numOfExpenseBudgetCategoriesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseBudgetCategories));
	        assertEquals("Number of Expense Budget Categories did not match", numOfExpenseBudgetCategoriesapp, NumOfExpenseBudgetCategories2016);
		}
		
		public void VerifyNumOfExpenseBudgetCategoriesbyCommittedExpenseBudget() throws SQLException {
		 	int NumOfExpenseBudgetCategories2016 =  NYCDatabaseUtil.getBudgetExpenseBudgetCategoriesCount(2016,'B');
	        int numOfExpenseBudgetCategoriesapp = Helper.stringToInt(BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5ExpenseBudgetCategoriesbyCommittedExpenseBudget));
	        assertEquals("Number of Expense Budget Categories did not match", numOfExpenseBudgetCategoriesapp, NumOfExpenseBudgetCategories2016);
		}
	*/	
	
}

