package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import utilities.NYCBaseTest;
import FunctionalSpending.SpendingWidgetTest;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;

@RunWith(Suite.class)
@SuiteClasses({

	SpendingWidgetTest.class,
	/* Need to fix path to retrieve count */
	// BudgetWidgetTest.class, 
	RevenueWidgetTest.class,
	PayrollWidgetTest.class
	
})
public class FunctionalTest extends NYCBaseTest{	
	
}
