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
import smoke.TrendsTest;
import utilities.NYCBaseTest;

@RunWith(Suite.class)
@SuiteClasses({
	SpendingTest.class,
	TotalSpendingTest.class,
	PayrollSpendingTest.class,
	TrendsTest.class,
	SmartSearch.class,
	DataFeedsTest.class,
	AdvancedSearchTest.class,
	//PrimaryNavigationTest.class,
	HomePageTest.class,
	OGESpendingTest.class
})
public class SmokeTest extends NYCBaseTest{
	
}
