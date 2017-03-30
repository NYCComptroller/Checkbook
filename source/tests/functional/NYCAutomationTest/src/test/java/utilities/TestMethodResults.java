package utilities;

public class TestMethodResults {
	
	//class variables
	private String TestName;
	private String TestResults;
	private boolean TestPassed;
	
	//constructors
	public TestMethodResults() {
		TestName = "";
		TestResults = "";
		TestPassed = false;
	}
	
	public TestMethodResults(String inTestName) {
		this();
		this.TestName = inTestName;
	}
	
	public TestMethodResults(String inTestName, String inTestResults) {
		this();
		this.TestName = inTestName;
		this.TestResults = inTestResults;
	}
	
	public TestMethodResults(String inTestName,  String inTestResults, boolean inTestPassed) {
		this();
		this.TestName = inTestName;
		this.TestResults = inTestResults;
		this.TestPassed = inTestPassed;
	}
	
	//getter/setter
	public String getTestName() {
		return TestName;
	}
	
	public void setTestName(String testName) {
		TestName = testName;
	}
	
	public String getTestResults() {
		return TestResults;
	}
	public void setTestResults(String testResults) {
		TestResults = testResults;
	}
	
	public void setTestPassed(boolean testPassed) {
		TestPassed = testPassed;
	}
	
	public boolean getTestPassed() {
		return TestPassed;
	}
}

