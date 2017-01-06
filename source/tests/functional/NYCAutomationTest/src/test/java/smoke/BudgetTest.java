package smoke;

import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;

public class BudgetTest extends NYCBaseTest{
	@Before
    public void GoToPage(){
	   if (!BudgetPage.isAt())
		   BudgetPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
