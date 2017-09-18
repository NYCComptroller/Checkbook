package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import utilities.NYCBaseTest;
import FunctionalPayroll.*;
import smoke.*;

@RunWith(Suite.class)
@SuiteClasses({
	SpendingTest.class
})
public class SmokeTest extends NYCBaseTest{
	
}
