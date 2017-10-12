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
//public class BudgetWidgetDetailsTest extends TestStatusReport{

public class BudgetWidgetDetailsTest extends NYCBaseTest {
	
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
		public void VerifyNumOfBudgetAgenciesTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
			
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
			
			String NumOfBudgetDetailsAmount2016 =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmount2016); 
		
		
		}
		
		/*
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
	
		*/
		/* ***************** Test Widget Transaction Total Amount ****************** */
	
		/* 
		@Test
		public void VerifyBudgetTransactionAmount() throws SQLException {
			//Float transactionAmt = 26.3f;
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5Agencies);
			HomePage.ShowWidgetDetails();
		//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
			String NumOfBudgetDetailsAmount2016 =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmount2016); 
		}
		*/
}

