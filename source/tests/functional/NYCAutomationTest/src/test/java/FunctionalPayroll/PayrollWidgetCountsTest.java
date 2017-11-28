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

//public class PayrollWidgetCountsTest extends NYCBaseTest {
	public class PayrollWidgetCountsTest extends TestStatusReport{
		int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
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
		Integer WidgetCountDB =  NYCDatabaseUtil.getPayrollAgenciesCount(year,'B');
		Integer WidgetCountApp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPayroll);
		assertEquals("Payroll Agencies widget count did not match with DB", WidgetCountApp, WidgetCountDB);
	}	
	
	@Test
	public void VerifyNumOfAgenciesbyOvertime() throws SQLException {
		Integer WidgetCountDB =  NYCDatabaseUtil.getPayrollAgenciesCount(year,'B');
		Integer WidgetCountApp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyOvertime);
		assertEquals("Payroll Agencies by Overtime widget count did not match with DB", WidgetCountApp, WidgetCountDB);
	}
	@Test
	public void VerifyNumOfPayrollAnnualSalaries() throws SQLException {
		Integer WidgetCountDB =  NYCDatabaseUtil.getPayrollSalCount(year,'B');
		Integer WidgetCountApp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AnnualSalaries);
		assertEquals("Payroll Agencies by Annual Salaries widget count did not match with DB",WidgetCountApp, WidgetCountDB);
	}
	
	/* issue with widget option
		@Test
		public void VerifyNumOfPayrollTitlesbyNumberofEmployees() throws SQLException {
		 	Integer WidgetCountDB =  NYCDatabaseUtil.getPayrollSalCount(year,'B');
			//Integer NumOfPayrollSalCount2016 =  322765;
		       Integer WidgetCountApp = PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5TitlesbyNumberofEmployees);
			        assertEquals("Number of Payroll salaried employees did not match", WidgetCountApp, WidgetCountDB);
		}
	 */
}

