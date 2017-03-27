package FunctionalPayroll;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import navigation.TopNavigation.Payroll;
//import navigation.TopNavigation.Contracts.ActiveExpenseContracts;
import pages.payroll.PayrollPage;
import pages.payroll.PayrollPage.WidgetOption;
//import pages.contracts.ActiveExpenseContractsPage;
//import pages.contracts.ContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utility.Helper;
import utility.TestStatusReport;

//public class PayrollwidgetTest   extends NYCBaseTest{
	public class PayrollwidgetTest   extends TestStatusReport{


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
	       int numOfPayrollAgenciesapp = Helper.stringToInt(PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyPayroll));
		        assertEquals("Number of Payroll Agenies did not match", numOfPayrollAgenciesapp, NumOfPayrollAgencies2016);
		}
		@Test
		public void VerifyNumOfAgenciesbyOvertime() throws SQLException {
		 	int NumOfPayrollAgencies2016 =  NYCDatabaseUtil.getPayrollAgenciesCount(2016,'B');
	       int numOfPayrollAgenciesapp = Helper.stringToInt(PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AgenciesbyOvertime));
		        assertEquals("Number of Payroll Agenies did not match", numOfPayrollAgenciesapp, NumOfPayrollAgencies2016);
		}
		@Test
		public void VerifyNumOfPayrollAnnualSalaries() throws SQLException {
		 	int NumOfPayrollSalCount2016 =  NYCDatabaseUtil.getPayrollSalCount(2016,'B');//getting error in dbutil count sql
			//int NumOfPayrollSalCount2016 =  322765;
	       int numOfPayrolleSalCountapp = Helper.stringToInt(PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5AnnualSalaries));
		        assertEquals("Number of Payroll salaried employees did not match", numOfPayrolleSalCountapp, NumOfPayrollSalCount2016);
		}
		/* issue with widget option
		@Test
		public void VerifyNumOfPayrollTitlesbyNumberofEmployees() throws SQLException {
		 	//int NumOfPayrollSalCount2016 =  NYCDatabaseUtil.getPayrollSalCount(2016,'B');
			int NumOfPayrollSalCount2016 =  322765;
		       int numOfPayrolleSalCountapp = Helper.stringToInt(PayrollPage.GetTop5WidgetTotalCount(WidgetOption.Top5TitlesbyNumberofEmployees));
			        assertEquals("Number of Payroll salaried employees did not match", numOfPayrolleSalCountapp, NumOfPayrollSalCount2016);
		}
		*/
		
}

