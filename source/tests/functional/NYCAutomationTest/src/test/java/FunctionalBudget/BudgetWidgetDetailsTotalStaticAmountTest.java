package FunctionalBudget;

import static org.junit.Assert.assertEquals;

import helpers.Helper;
import navigation.TopNavigation.Contracts.RegisteredRevenueContracts;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.budget.BudgetPage.WidgetOption;
import pages.contracts.RegisteredRevenueContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;
public class BudgetWidgetDetailsTotalStaticAmountTest extends TestStatusReport{

//public class BudgetWidgetDetailsTotalStaticAmountTest extends NYCBaseTest {
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
		@Before
	    public void GoToPage(){
			BudgetPage.GoTo();
			 if (!BudgetPage.isAt())
				 BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/* ***************** Test Widget Details Counts ****************** */
		
		//public void VerifyNumOfBudgetAgencies() throws SQLException { 
			//Integer NumOfBudgetAgencies2016 =  NYCDatabaseUtil.getBudgetAgenciesCount(2016,'B');
			//Integer numOfBudgetAgenciesapp = BudgetPage.GetTop5WidgetTotalCount(WidgetOption.Top5Agencies);
			//assertEquals("Number of Budget Agencies did not match", numOfBudgetAgenciesapp, NumOfBudgetAgencies2016);
		//}
		
		@Test
	    public void VerifyBudgetTopNavigationAmount() throws SQLException {
	        String TopNavBudgetAmtDB = NYCDatabaseUtil.getBudgetAmount(year, 'B');
	        String TopNavBudgetAmtApp = BudgetPage.GetBudgetAmount();
	        assertEquals("Budget Top Navigation Amount did not match", TopNavBudgetAmtApp, TopNavBudgetAmtDB);
	    }
		@Test
		public void VerifyNumOfBudgetAgenciesTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
			
			String NumOfBudgetDetailsAmount2016 =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		    assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmount2016); 
		
		}
		
		
		@Test
		public void VerifyBudgetAgenciesTransactionAmount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
		
		
		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudgetTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
		@Test
		public void VerifyNumOfBudgetAgenciesbyPercentDifference() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategories() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategories);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyCommittedExpenseBudget() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
		@Test
		public void VerifyNumOfExpenseCategoriesbyPercentDifference() throws SQLException {			
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5ExpenseCategoriesbyPercentDifference);
			HomePage.ShowWidgetDetails();
			String NumOfBudgetDetailsAmountDB =  NYCDatabaseUtil.getBudgetDetailsAmount(year,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Budget Domain widget Details page Total Modified amount did not match with DB", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmountDB); 
		}
	
		
		/* ***************** Test Widget Transaction Total Amount ****************** */
	
	
}

