package smoke;

import static org.junit.Assert.assertTrue;

import org.junit.Before;
import org.junit.Test;

import pages.budget.BudgetPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utility.Helper;
import utility.TestStatusReport;

public class BudgetTest extends TestStatusReport{
	@Before
    public void GoToPage(){
	   if (!BudgetPage.isAt())
		   BudgetPage.GoTo();
	   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
		   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
    }
	
}
