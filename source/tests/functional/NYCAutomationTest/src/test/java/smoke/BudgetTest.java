package smoke;

import helpers.Helper;

import org.junit.Before;
import pages.budget.BudgetPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.TestStatusReport;

public class BudgetTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!BudgetPage.isAt())
		   BudgetPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
