package FunctionalPayroll;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.payroll.PayrollPage;
import pages.payroll.PayrollPage.WidgetOption;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;

public class PayrollWidgetTest extends TestStatusReport {
		@Before
	    public void GoToPage(){
			PayrollPage.GoTo();
		 
		   if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			   HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		   HomePage.ShowWidgetDetails();
	    }	
		
		/* ***************** Test Widget Counts ****************** */
		@Test
		public void VerifyNumOfAgenciesbyPayroll() throws SQLException {
			int NumOfPayrollAgencies2016 =  NYCDatabaseUtil.getPayrollAgenciesCount(2016,'B');
			int numOfPayrollAgenciesapp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPayroll);
			assertEquals("Number of Payroll Agenies did not match", numOfPayrollAgenciesapp, NumOfPayrollAgencies2016);
		}
		@Test
		public void VerifyNumOfAgenciesbyOvertime() throws SQLException {
			int NumOfPayrollAgencies2016 =  NYCDatabaseUtil.getPayrollAgenciesCount(2016,'B');
			int numOfPayrollAgenciesapp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyOvertime);
			assertEquals("Number of Payroll Agenies did not match", numOfPayrollAgenciesapp, NumOfPayrollAgencies2016);
		}
		@Test
		public void VerifyNumOfPayrollAnnualSalaries() throws SQLException {
			int NumOfPayrollSalCount2016 =  NYCDatabaseUtil.getPayrollSalCount(2016,'B');
			int numOfPayrolleSalCountapp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AnnualSalaries);
			assertEquals("Number of Payroll salaried employees did not match", numOfPayrolleSalCountapp, NumOfPayrollSalCount2016);
		}
}

