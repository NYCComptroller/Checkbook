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
//	TotalSpendingWidgetTest.class,
//	TotalSpendingWidgetDetailsTest.class,
//	TotalSpendingTitlesTest.class,
	//TotalSpendingWidgetDetailsTest.class,
	//PayrollSpendingWidgetDetailsTest.class,
	PayrollSpendingWidgetTest.class
	//CapitalSpendingWidgetDetailsTest.class,
	//ContractsSpendingWidgetDetailsTest.class,
	//TrustAgencySpendingWidgetDetailsTest.class,
	//OtherSpendingWidgetDetailsTest.class
	
	/* Contracts */
	//ActiveExpenseContractsTest.class,
//	ActiveExpenseContractsDetailsTest.class,
	//ActiveRevenueContractsTest.class,
//	ActiveRevenueContractsDetailsTest.class,
	//PendingExpenseContractsTest.class,
//PendingExpenseContractsDetailsTest.class,
	//PendingRevenueContractsTest.class,
//	PendingRevenueContractsDetailsTest.class,
	//RegisteredExpenseContractsTest.class,
//	RegisteredExpenseContractsDetailsTest.class,
	//RegisteredRevenueContractsTest.class,
//	RegisteredRevenueContractsDetailsTest.class,
 
	/* Budget */
	/* Need to fix path to retrieve count */
	//BudgetWidgetDetailsTest.class,
	//BudgetWidgetDetailsAmountTest.class,
	
	//BudgetWidgetTest.class,
	//BudgetWidgetTitles.class,
	//BudgetWidgetDetailsPageTitlesTest.class,

	/* Revenue */
//	RevenueWidgetTest.class,
//RevenueWidgetTitles.class,
	//RevenueWidgetDetailsTest.class,
	//RevenueWidgetDetailsAmountTest.class,
	//RevenueWidgetDetailsPageTitlesTest.class,

	/* Payroll */
	//PayrollWidgetTest.class,
	
	//PayrollWidgetDetailsTest.class,

	//PayrollTitles.class
	//PayrollWidgetDetailsPageTitlesTest.class,
})
public class FunctionalTest extends NYCBaseTest{	
	
}
