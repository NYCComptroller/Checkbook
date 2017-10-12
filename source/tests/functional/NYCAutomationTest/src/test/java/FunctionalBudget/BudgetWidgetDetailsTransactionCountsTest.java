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
//public class BudgetAllWidgetDetailsTest extends TestStatusReport{
public class BudgetWidgetDetailsTransactionCountsTest extends NYCBaseTest {

	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
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
		@Test
		public void VerifyNumOfBudgetAgencies() throws SQLException { 
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}	
		
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudgetTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {			
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCountDB =  NYCDatabaseUtil.getBudgetDetailsCount(year,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCountDB); 
		}
	
		
	
}

