package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import functional.*;
import FunctionalSpending.*;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;
import utilities.NYCBaseTest;

@RunWith(Suite.class)
@SuiteClasses({

//	SpendingWidgetTest.class,
//	/* Need to fix path to retrieve count */
//	// BudgetWidgetTest.class, 
//	RevenueWidgetTest.class,
//	PayrollWidgetTest.class
	
	
	ActiveExpenseContractsTest.class,
	ActiveExpenseContractsDetailsTest.class,
	ActiveRevenueContractsTest.class,
	ActiveRevenueContractsDetailsTest.class,
	PendingExpenseContractsTest.class,
	PendingExpenseContractsDetailsTest.class,
	PendingRevenueContractsTest.class,
	PendingRevenueContractsDetailsTest.class,
	RegisteredExpenseContractsTest.class,
	RegisteredExpenseContractsDetailsTest.class,
	RegisteredRevenueContractsTest.class,
	RegisteredRevenueContractsDetailsTest.class,
	BudgetWidgetTest.class,
	PayrollWidgetTest.class,
	PayrollTitles.class,
	PayrollWidgetDetailsTest.class
	
	
})
public class FunctionalTest extends NYCBaseTest{	
	
}
