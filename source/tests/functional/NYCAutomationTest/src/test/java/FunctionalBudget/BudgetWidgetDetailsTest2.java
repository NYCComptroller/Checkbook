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

public class BudgetWidgetDetailsTest2 extends NYCBaseTest {
	
		@Before
	    public void GoToPage(){
			BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		
		/* ***************** Test Widget Details Counts ****************** */
		

		@Test
		public void VerifyNumOfBudgetAgenciesbyCommittedExpenseBudgetTransactionCount() throws SQLException {
			BudgetPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyCommittedExpenseBudget);
			HomePage.ShowWidgetDetails();
			int NumOfBudgetDetailsCount2016 =  NYCDatabaseUtil.getBudgetDetailsCount(2016,'B');
			int numOfBudgetDetailsCountapp = BudgetPage.GetTransactionCount();
			assertEquals("Number of transactions for Budget Details page table  did not match", numOfBudgetDetailsCountapp, NumOfBudgetDetailsCount2016); 
		
		String NumOfBudgetDetailsAmount2016 =  NYCDatabaseUtil.getBudgetDetailsAmount(2016,'B');
			String numOfBudgetDetailsAmountapp = HomePage.GetTransactionAmount1();
		assertEquals("Number ofRevenue widget Details page table count did not match", numOfBudgetDetailsAmountapp, NumOfBudgetDetailsAmount2016); 
		
		
		}
		

}

