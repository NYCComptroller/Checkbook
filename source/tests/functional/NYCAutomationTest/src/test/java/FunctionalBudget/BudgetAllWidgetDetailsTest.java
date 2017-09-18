package FunctionalBudget;

import static org.junit.Assert.assertEquals;

import helpers.Helper;
import navigation.TopNavigation.Spending.TotalSpending;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;

import pages.home.HomePage;
import pages.spending.TotalSpendingPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
public class BudgetAllWidgetDetailsTest extends TestStatusReport{
//public class BudgetAllWidgetDetailsTest extends NYCBaseTest {

	
		@Before
	    public void GoToPage(){
			BudgetPage.GoTo();
			if (!BudgetPage.isAt()){
				BudgetPage.GoTo();
			}
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
			
		 
	    }
		
		/* ***************** Test Widget Details Counts ****************** */
		
		//public void VerifyNumOfBudgetAgencies() throws SQLException { 
			//Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			//Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			//assertEquals("Number of Budget Agencies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		//}

		
		
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudgetTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {			
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		}
	
		
	
}

