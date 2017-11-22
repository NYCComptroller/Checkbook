package FunctionalPayroll;

import static org.junit.Assert.assertEquals;
import helpers.Helper;

import java.sql.SQLException;

import org.junit.Before;
import org.junit.Test;

import pages.payroll.PayrollPage;
import pages.payroll.PayrollPage.WidgetOption;
import pages.contracts.ContractsPage;
import pages.home.HomePage;
import utilities.NYCBaseTest;
import utilities.NYCDatabaseUtil;
import utilities.TestStatusReport;


//public class PayrollWidgetDetailsPageTitlesTest extends NYCBaseTest {
	public class PayrollWidgetDetailsPageTitlesTest extends TestStatusReport {
	
	int year =  Integer.parseInt(NYCBaseTest.prop.getProperty("year"));
	@Before
	public void GoToPage(){
		PayrollPage.GoTo();

		if(!(Helper.getCurrentSelectedYear()).equalsIgnoreCase(NYCBaseTest.prop.getProperty("CurrentYear")))
			HomePage.SelectYear(NYCBaseTest.prop.getProperty("CurrentYear"));
		HomePage.ShowWidgetDetails();
	}

	/* ***************** Test Widget Transaction Count ****************** */
	/*
	@Test
	public void VerifyPayrollTransactionTitle() throws SQLException {
			PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		HomePage.ShowWidgetDetails();
	//assertTrue(HomePage.GetTransactionAmount1()== transactionAmt);
		String AgenciesTitle =  "Payroll Summary by Agency Title";
		String RevenueAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Payroll Agencies Widget details page title did not match", AgenciesTitle, RevenueAgenciesTitleApp); 
	}
	*/
	@Test
	public void VerifyTop5AgenciesbyPayrollTransactionTitle() throws SQLException{
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyPayroll);
		//HomePage.ShowWidgetDetails();
		String PayrollAgenciesTitle =  "Payroll Summary by Agency Title";
		String PayrollAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Payroll Agencies Widget details page title did not match", PayrollAgenciesTitle, PayrollAgenciesTitleApp);  
	}
	
	@Test
	public void VerifyTop5AgenciesbyOvertimeTransactionTitle() throws SQLException{
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AgenciesbyOvertime);
		//HomePage.ShowWidgetDetails();
		String PayrollAgenciesTitle =  "Payroll Summary by Agency Title";
		String PayrollAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Payroll Agencies Widget details page title did not match",PayrollAgenciesTitle, PayrollAgenciesTitleApp);  
	}
	
	@Test
	public void VerifyNumOfPayrollAnnualSalariesTransactionTitle() throws SQLException {
		PayrollPage.GoToTop5DetailsPage(WidgetOption.Top5AnnualSalaries);
		//HomePage.ShowWidgetDetails();
		String PayrollAgenciesTitle =  "Payroll Summary by Agency Title";
		String PayrollAgenciesTitleApp = HomePage.DetailsPagetitle();
	assertEquals("Payroll Agencies Widget details page title did not match", PayrollAgenciesTitle, PayrollAgenciesTitleApp); 
	}

	

}
