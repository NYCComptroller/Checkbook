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
//public class BudgetWidgetDetailsAmountTest extends TestStatusReport{

public class BudgetWidgetDetailsAmountTest extends NYCBaseTest {
	
		@Before
	    public void GoToPage(){
			 if (!BudgetPage.isAt())
				   BudgetPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }
		

	/* ***************** Test Widget Transaction Total Amount ****************** */
	
		
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

}

