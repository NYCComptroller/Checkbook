package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import smoke.AdvancedSearchTest;
import smoke.DataFeedsTest;
import smoke.HomePageTest;
import smoke.OGESpendingTest;
import smoke.PayrollSpendingTest;
import smoke.PrimaryNavigationTest;
import smoke.SmartSearch;
import smoke.SpendingTest;
import smoke.TotalSpendingTest;
import smoke.MWBESpendingTest;
import smoke.TrendsTest;
import smoke.BudgetTest;
import smoke.RevenueTest;
import smoke.PayrollTest;
import utilities.NYCBaseTest;

@RunWith(Suite.class)
@SuiteClasses({
	SpendingTest.class,
	//TotalSpendingTest.class,
	//PayrollSpendingTest.class,
	//TrendsTest.class,
	//SmartSearch.class,
	//DataFeedsTest.class,
	//AdvancedSearchTest.class,
	//PrimaryNavigationTest.class,
	//HomePageTest.class,
	//BudgetTest.class
	//OGESpendingTest.class
	//MWBESpendingTest.class
	//PayrollTest.class
	//RevenueTest.class
	
})
public class SmokeTest extends NYCBaseTest{
	
}
