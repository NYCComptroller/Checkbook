package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import FunctionalContracts.*;
import FunctionalSpending.*;
import FunctionalBudget.*;
import FunctionalRevenue.*;
import FunctionalPayroll.*;
import utilities.NYCBaseTest;

@RunWith(Suite.class)
@SuiteClasses({
	
	/* Spending */
	SpendingWidgetTest.class,

	/* Contracts */
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

	/* Budget */
	/* Need to fix path to retrieve count */
	BudgetWidgetTest.class,

	/* Revenue */
	RevenueWidgetTest.class,

	/* Payroll */
	PayrollWidgetTest.class,
	PayrollTitles.class,
	PayrollWidgetDetailsTest.class
})
public class FunctionalTest extends NYCBaseTest{	
	
}
