package suites;

import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

import utilities.NYCBaseTest;
import FunctionalPayroll.*;

@RunWith(Suite.class)
@SuiteClasses({
	PayrollWidgetTest.class
})
public class SmokeTest extends NYCBaseTest{
	
}
