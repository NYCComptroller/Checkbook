package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import functional.ActiveExpenseContractsDetailsTest;
import functional.ActiveExpenseContractsTest;
import functional.ActiveRevenueContractsDetailsTest;
import functional.ActiveRevenueContractsTest;
import functional.PendingExpenseContractsDetailsTest;
import functional.PendingExpenseContractsTest;
import functional.PendingRevenueContractsDetailsTest;
import functional.PendingRevenueContractsTest;
import functional.RegisteredExpenseContractsDetailsTest;
import functional.RegisteredExpenseContractsTest;
import functional.RegisteredRevenueContractsDetailsTest;
import functional.RegisteredRevenueContractsTest;
import FunctionalBudget.BurgetwidgetTest;
import smoke.AdvancedSearchTest;
import smoke.DataFeedsTest;
import smoke.HomePageTest;
import smoke.OGESpendingTest;
import smoke.PayrollSpendingTest;
import smoke.PrimaryNavigationTest;
import smoke.SmartSearch;
import smoke.SpendingTest;
import smoke.TotalSpendingTest;
import smoke.TrendsTest;
import utilities.NYCBaseTest;

@RunWith(Suite.class)
@SuiteClasses({
	//ActiveExpenseContractsTest.class,
	//ActiveExpenseContractsDetailsTest.class,
	//ActiveRevenueContractsTest.class,
	//ActiveRevenueContractsDetailsTest.class,
	//PendingExpenseContractsTest.class,
	//PendingExpenseContractsDetailsTest.class,
	//PendingRevenueContractsTest.class,
	//PendingRevenueContractsDetailsTest.class,
	//RegisteredExpenseContractsTest.class,
	//RegisteredExpenseContractsDetailsTest.class,
	//RegisteredRevenueContractsTest.class,
	//RegisteredRevenueContractsDetailsTest.class,
	BurgetwidgetTest.class
})
public class FunctionalTest extends NYCBaseTest{	
	
}
