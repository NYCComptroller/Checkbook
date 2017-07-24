package FunctionalBudget;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import helpers.Helper;

import java.sql.SQLException;
import java.util.Arrays;

import org.junit.Before;
import org.junit.Test;

import pages.home.HomePage;
import pages.budget.BudgetPage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;

public class BudgetWidgetTitles  extends NYCBaseTest{
	
	@Before
    public void GoToPage(){
	   if (!BudgetPage.isAt())
		   BudgetPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }

	@Test
    public void VerifyBudgetAmount() throws SQLException {
        String TotalBudgetAmtFY2016 = NYCDatabaseUtil.getBudgetAmount(2016, 'B');
        String budgetAmt = BudgetPage.GetBudgetAmount();
        assertEquals("Spending Amount did not match", budgetAmt, TotalBudgetAmtFY2016);
    }
	
	@Test
    public void VerifyBudgetDomainVisualizationsTitles(){
	    String[] sliderTitles= {"Expense Budget", 
	    						"Fiscal Year Comparisons", 
	    						"Top Ten Expense Categories by Expense Budget", 
	    						"Top Ten Agencies by Expense Budget"};  
    	assertTrue(Arrays.equals(sliderTitles, BudgetPage.VisualizationTitles().toArray()));
    }
	 
	@Test
    public void VerifyBudgetWidgetTitles(){
	    String[] widgetTitles = {"Top 5 Agencies",
	    						"Top 5 Agencies by Committed Expense Budget",
	    						"Top 5 Agencies by Percent Difference",
	    						"Top 5 Expense Categories",
	    						"Top 5 Expense Categories by Committed Expense Budget",
	    						"Top 5 Expense Categories by Percent Difference"};  
    	//HomePage.ShowWidgetDetails();
    	assertTrue(Arrays.equals(widgetTitles, BudgetPage.WidgetTitles().toArray()));
    	//assertEquals("Budget Title did not match", widgetTitles,  BudgetPage.WidgetTitles().toArray());
	}
    
	
	
}