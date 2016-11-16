package smoke;

import static org.junit.Assert.assertEquals;

import org.junit.Test;

import pages.PayrollSpendingPage;
import pages.SpendingPage;
import utilities.NYCBaseTest;


public class SmokeTest extends NYCBaseTest{
	
	@Test
	public void VerifySpendingAmount(){
		String TotalSpendingAmtFY2016 = "$94.9B";
		
		SpendingPage.GoTo();
		String spendingAmt = SpendingPage.GetSpendingAmount();
		
		assertEquals("Spending Amount did not match", spendingAmt, TotalSpendingAmtFY2016);
		
	}
	
	@Test
	public void VerifyNumOfAgenciesPayrollSpendng(){
		String PayrollSpendingNumOfAgenciesFY2016 = "130";
		
		PayrollSpendingPage.GoTo();
		String numberOfAgencies = PayrollSpendingPage.GetTotalNumOfAgencies();
		
		assertEquals("Number of Agencies in Payroll Spending did not match", numberOfAgencies, PayrollSpendingNumOfAgenciesFY2016);
	}

		
}
