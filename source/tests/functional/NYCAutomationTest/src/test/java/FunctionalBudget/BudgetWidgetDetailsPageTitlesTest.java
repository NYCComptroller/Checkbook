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
public class BudgetWidgetDetailsPageTitlesTest extends TestStatusReport{

//	public class BudgetWidgetDetailsPageTitlesTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
		@Before
	    public void GoToPage(){
			// if (!BudgetPage.isAt())
				   BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		

	/* ***************** Test Widget Transaction Total Amount ****************** */
	
		
		@Test
		public void VerifyTitleofBudgetAgenciesTransaction() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
	     	String WidgetDetailsPageTitle =  "Agencies Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
		assertEquals("Budget Domain Agencies Widget Transaction Title did not match", WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
		
		@Test
		public void VerifyTitleofBudgetAgenciesbyCommittedExpenseBudgetTransaction() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			String WidgetDetailsPageTitle =  "Agencies by Committed Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
			assertEquals("Budget Domain Agencies by Committed Expense Budget Widget Transaction Title did not match", WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
		@Test
		public void VerifyTitleofBudgetAgenciesbyPercentDifferenceTransaction() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			String WidgetDetailsPageTitle =  "Agencies by Percent Difference Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
			assertEquals("Budget Domain Agencies by Percent Difference Widget Transaction Title did not match",WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
		@Test
		public void VerifyTitleofExpenseCategoriesTransaction() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
			HomePage.ShowWidgetDetails();
			String WidgetDetailsPageTitle =  "Expense Categories Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
			assertEquals("Budget Domain Expense Categoriess Widget Transaction Title did not match", WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
		@Test
		public void VerifyTitleOfExpenseCategoriesbyCommittedExpenseBudgetTransaction() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			String WidgetDetailsPageTitle =  "Expense Categories by Committed Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
			assertEquals("Budget Domain Expense Categories by Committed Expense Budget Widget Transaction Title did not match", WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
		@Test
		public void VerifyTitleOfExpenseCategoriesbyPercentDifferenceTransaction() throws SQLException {			
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			String WidgetDetailsPageTitle =  "Expense Categories by Percent Difference Expense Budget Transactions";
			String WidgetDetailsPageApp = HomePage.DetailsPagetitle();
			assertEquals("Budget Domain Expense Categories by Percent Difference Widget Transaction Title did not match", WidgetDetailsPageTitle, WidgetDetailsPageApp); 
		}
	
}

